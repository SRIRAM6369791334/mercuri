<?php

namespace App\Services;

use App\Models\Citylist;
use App\Models\Customer;
use App\Models\Dropdowndata;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Statelist;
use Exception;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class LeadImportService
{
    protected LeadCreationService $creationService;

    // In-memory lookup caches
    protected array $leadSources = [];
    protected array $leadStatuses = [];
    protected int $defaultStatusId = 342; // WARM
    protected int $expoMasterSourceId = 391;
    protected array $productsById = [];
    protected array $productsByName = [];
    protected array $productsByModel = [];
    protected array $statesByName = [];
    protected array $citiesByName = []; // city_name_lower => city_id
    protected array $existingDbLeads = []; // normalized_phone => Lead
    protected array $existingDbCustomers = []; // normalized_phone => Customer

    public function __construct(LeadCreationService $creationService)
    {
        $this->creationService = $creationService;
    }

    /**
     * Preload all lookups to prevent N+1 queries.
     */
    protected function preloadLookups(): void
    {
        // 1. Lead Sources (formid = 4)
        $sources = Dropdowndata::where('formid', 4)->get();
        foreach ($sources as $s) {
            $key = strtolower(trim($s->dropdowndata));
            $this->leadSources[$key] = (int)$s->id;
            if ($key === 'expo') {
                $this->expoMasterSourceId = (int)$s->id;
            }
        }

        // 2. Lead Statuses (formid = 6)
        $statuses = Dropdowndata::where('formid', 6)->orderBy('orderno', 'asc')->get();
        foreach ($statuses as $st) {
            $key = strtolower(trim($st->dropdowndata));
            $this->leadStatuses[$key] = (int)$st->id;
        }
        // Fallbacks for "new", "open"
        if (isset($this->leadStatuses['warm'])) {
            $this->defaultStatusId = $this->leadStatuses['warm'];
        } elseif (isset($this->leadStatuses['cold'])) {
            $this->defaultStatusId = $this->leadStatuses['cold'];
        } else {
            $this->defaultStatusId = reset($this->leadStatuses) ?: 342;
        }
        $this->leadStatuses['new'] = $this->defaultStatusId;
        $this->leadStatuses['open'] = $this->defaultStatusId;

        // 3. Products
        $products = DB::table('products')->select('id', 'productname', 'productmodel')->get();
        foreach ($products as $p) {
            $this->productsById[(string)$p->id] = (int)$p->id;
            if (!empty($p->productname)) {
                $this->productsByName[strtolower(trim($p->productname))] = (int)$p->id;
            }
            if (!empty($p->productmodel)) {
                $this->productsByModel[strtolower(trim($p->productmodel))] = (int)$p->id;
            }
        }

        // 4. States & Cities
        $states = Statelist::all();
        foreach ($states as $s) {
            $this->statesByName[strtolower(trim($s->state))] = (int)$s->id;
        }

        $cities = Citylist::all();
        foreach ($cities as $c) {
            $cityName = strtolower(trim($c->city_name));
            if (!isset($this->citiesByName[$cityName])) {
                $this->citiesByName[$cityName] = (int)$c->id;
            }
        }

        // 5. Existing active leads
        $leads = Lead::where('soft_delete', 0)->get(['id', 'lead_id', 'phonumber', 'LeadName', 'contactname', 'customeriid']);
        foreach ($leads as $l) {
            $phone = $this->normalizePhone($l->phonumber);
            if ($phone && !isset($this->existingDbLeads[$phone])) {
                $this->existingDbLeads[$phone] = $l;
            }
        }

        // 6. Existing active customers
        $customers = Customer::where('soft_d', 0)->get(['id', 'cusid', 'contact_no', 'customername']);
        foreach ($customers as $c) {
            $phone = $this->normalizePhone($c->contact_no);
            if ($phone && !isset($this->existingDbCustomers[$phone])) {
                $this->existingDbCustomers[$phone] = $c;
            }
        }
    }

    /**
     * Import leads from uploaded spreadsheet file.
     *
     * @param string $filePath Absolute path to uploaded file
     * @param string $type 'general' | 'expo'
     * @param bool $updateExisting Whether to update existing empty fields
     * @param string|null $defaultExpoEvent Default Expo Event name if importing expo
     * @param int|null $userId Auth user ID
     * @return array
     */
    public function import(
        string $filePath,
        string $type = 'general',
        bool $updateExisting = false,
        ?string $defaultExpoEvent = null,
        ?int $userId = null
    ): array {
        $this->preloadLookups();

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        $highestCol = $sheet->getHighestColumn();

        // Build header index map
        $headerMap = [];
        for ($col = 'A'; $col <= $highestCol; $col++) {
            $rawHeader = (string)$sheet->getCell($col . '1')->getValue();
            $canonical = $this->normalizeHeader($rawHeader);
            if ($canonical) {
                $headerMap[$canonical] = $col;
            }
        }

        // Verify required phone header exists
        if (!isset($headerMap['phone'])) {
            return [
                'success' => false,
                'message' => 'Invalid file format: Could not find "Phone Number" or "Contact No" column header.',
                'total_rows' => 0,
                'imported' => 0,
                'updated' => 0,
                'skipped' => 0,
                'failed' => 0,
                'skipped_rows' => [],
                'error_rows' => [],
            ];
        }

        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $failed = 0;
        $skippedRows = [];
        $errorRows = [];
        $processedPhonesInFile = [];

        $totalRows = max(0, $highestRow - 1);

        for ($rowNum = 2; $rowNum <= $highestRow; $rowNum++) {
            // Extract raw row data
            $rawPhone = $this->getCellValue($sheet, $headerMap['phone'] ?? null, $rowNum);
            $rawName = $this->getCellValue($sheet, $headerMap['name'] ?? null, $rowNum);
            $rawCompany = $this->getCellValue($sheet, $headerMap['company'] ?? null, $rowNum);
            $rawStatus = $this->getCellValue($sheet, $headerMap['status'] ?? null, $rowNum);
            $rawSource = $this->getCellValue($sheet, $headerMap['source'] ?? null, $rowNum);
            $rawProduct = $this->getCellValue($sheet, $headerMap['product'] ?? null, $rowNum);
            $rawDealValue = $this->getCellValue($sheet, $headerMap['deal_value'] ?? null, $rowNum);
            $rawEmail = $this->getCellValue($sheet, $headerMap['email'] ?? null, $rowNum);
            $rawRemarks = $this->getCellValue($sheet, $headerMap['remarks'] ?? null, $rowNum);
            $rawDate = $this->getCellValue($sheet, $headerMap['date'] ?? null, $rowNum);
            $rawState = $this->getCellValue($sheet, $headerMap['state'] ?? null, $rowNum);
            $rawCity = $this->getCellValue($sheet, $headerMap['city'] ?? null, $rowNum);
            $rawAddress = $this->getCellValue($sheet, $headerMap['address'] ?? null, $rowNum);
            $rawGst = $this->getCellValue($sheet, $headerMap['gst'] ?? null, $rowNum);

            // Skip completely blank rows
            if (empty($rawPhone) && empty($rawName) && empty($rawCompany)) {
                continue;
            }

            // 1. Phone validation & normalization
            $normalizedPhone = $this->normalizePhone($rawPhone);
            if (!$normalizedPhone) {
                $failed++;
                $errorRows[] = [
                    'row' => $rowNum,
                    'phone' => (string)$rawPhone,
                    'reason' => 'Invalid phone number (must be a valid 10-digit mobile number).'
                ];
                continue;
            }

            // 2. Name / Company validation
            $name = trim((string)$rawName);
            $company = trim((string)$rawCompany);
            if (empty($name) && empty($company)) {
                $failed++;
                $errorRows[] = [
                    'row' => $rowNum,
                    'phone' => $normalizedPhone,
                    'reason' => 'Both Name and Company Name are empty.'
                ];
                continue;
            }
            if (empty($name)) $name = $company;
            if (empty($company)) $company = $name;

            // 3. Duplicate check within file
            if (isset($processedPhonesInFile[$normalizedPhone])) {
                $skipped++;
                $skippedRows[] = [
                    'row' => $rowNum,
                    'phone' => $normalizedPhone,
                    'reason' => "Duplicate in file (already appeared at row {$processedPhonesInFile[$normalizedPhone]})."
                ];
                continue;
            }
            $processedPhonesInFile[$normalizedPhone] = $rowNum;

            // 4. Resolve Status
            $statusId = $this->resolveStatus($rawStatus);

            // 5. Resolve Source
            if ($type === 'expo') {
                $sourceId = $this->expoMasterSourceId;
                $expoEvent = !empty($rawSource) ? trim((string)$rawSource) : ($defaultExpoEvent ?: 'Expo');
            } else {
                $expoEvent = null;
                $resolvedSource = $this->resolveGeneralSource($rawSource);
                if ($resolvedSource['is_expo']) {
                    $skipped++;
                    $skippedRows[] = [
                        'row' => $rowNum,
                        'phone' => $normalizedPhone,
                        'reason' => 'Source indicates Expo. Please use Expo Leads bulk upload.'
                    ];
                    continue;
                }
                $sourceId = $resolvedSource['id'];
            }

            // 6. Resolve Product
            $productId = $this->resolveProduct($rawProduct);

            // 7. Resolve Date
            $parsedDate = $this->parseDate($sheet->getCell(($headerMap['date'] ?? 'A') . $rowNum), $rawDate);

            // 8. Resolve State & City
            $stateId = $this->resolveState($rawState);
            $cityId = $this->resolveCity($rawCity);

            $rowData = [
                'name' => $name,
                'company_name' => $company,
                'contact_name' => $name,
                'phone' => $normalizedPhone,
                'email' => !empty($rawEmail) ? trim((string)$rawEmail) : null,
                'lead_status_id' => $statusId,
                'lead_source_id' => $sourceId,
                'product_id' => $productId,
                'deal_value' => is_numeric($rawDealValue) ? (float)$rawDealValue : null,
                'remarks' => !empty($rawRemarks) ? trim((string)$rawRemarks) : null,
                'entry_date' => $parsedDate,
                'state_id' => $stateId,
                'city_id' => $cityId,
                'address' => !empty($rawAddress) ? trim((string)$rawAddress) : null,
                'gst' => !empty($rawGst) ? trim((string)$rawGst) : null,
                'expo_event' => $expoEvent,
            ];

            // 9. Check existing in DB
            $existingLead = $this->existingDbLeads[$normalizedPhone] ?? null;

            if ($existingLead) {
                if ($updateExisting) {
                    try {
                        $this->creationService->updateExistingLead($existingLead, $rowData, $type);
                        $updated++;
                    } catch (Exception $e) {
                        $failed++;
                        $errorRows[] = [
                            'row' => $rowNum,
                            'phone' => $normalizedPhone,
                            'reason' => 'Failed to update existing lead: ' . $e->getMessage()
                        ];
                    }
                } else {
                    $skipped++;
                    $skippedRows[] = [
                        'row' => $rowNum,
                        'phone' => $normalizedPhone,
                        'reason' => "Phone already exists in system ({$existingLead->lead_id})."
                    ];
                }
                continue;
            }

            // 10. Create new Lead
            try {
                if ($type === 'expo') {
                    $newLead = $this->creationService->createExpoLead($rowData, $userId);
                } else {
                    $newLead = $this->creationService->createGeneralLead($rowData, $userId);
                }
                $imported++;
                $this->existingDbLeads[$normalizedPhone] = $newLead;
            } catch (Exception $e) {
                $failed++;
                $errorRows[] = [
                    'row' => $rowNum,
                    'phone' => $normalizedPhone,
                    'reason' => 'Database error on row: ' . $e->getMessage()
                ];
            }
        }

        return [
            'success' => true,
            'message' => "Import complete: {$imported} leads imported, {$updated} updated, {$skipped} skipped, {$failed} failed.",
            'total_rows' => $totalRows,
            'imported' => $imported,
            'updated' => $updated,
            'skipped' => $skipped,
            'failed' => $failed,
            'skipped_rows' => $skippedRows,
            'error_rows' => $errorRows,
        ];
    }

    /**
     * Map arbitrary header text to canonical key.
     */
    protected function normalizeHeader(string $header): ?string
    {
        $h = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $header)));
        if (empty($h)) return null;

        $map = [
            'name' => ['name', 'contactname', 'personname', 'clientname', 'fullname'],
            'company' => ['companyname', 'company', 'leadname', 'customername', 'firmname', 'organization'],
            'phone' => ['phonenumber', 'phone', 'contactno', 'contactnumber', 'mobile', 'mobileno', 'phoneno'],
            'email' => ['email', 'emailid', 'mail', 'emailaddress'],
            'status' => ['leadstatus', 'status', 'currentstatus'],
            'source' => ['leadsource', 'source', 'sourceofenquiry', 'expoevent', 'exponame', 'event'],
            'product' => ['product', 'products', 'productname', 'model', 'item', 'singleproduct'],
            'deal_value' => ['dealvalue', 'value', 'amount', 'budget', 'rate'],
            'remarks' => ['remarks', 'remark', 'notes', 'enquirydetail', 'requirement', 'description'],
            'date' => ['date', 'entrydate', 'enquirydate', 'createddate'],
            'state' => ['state', 'statename'],
            'city' => ['city', 'cityname', 'location'],
            'address' => ['address', 'billaddress', 'billingaddress', 'street'],
            'gst' => ['gst', 'gstno', 'gstnumber'],
        ];

        foreach ($map as $canonical => $synonyms) {
            if (in_array($h, $synonyms)) {
                return $canonical;
            }
        }

        return null;
    }

    /**
     * Clean phone number into valid 10-digit mobile.
     */
    public function normalizePhone($value): ?string
    {
        if ($value instanceof RichText) {
            $value = $value->getPlainText();
        }
        $str = (string)$value;
        // Strip non-digits
        $digits = preg_replace('/[^0-9]/', '', $str);

        // If 12 digits starting with 91, strip 91
        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            $digits = substr($digits, 2);
        }
        // If 11 digits starting with 0, strip 0
        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        if (strlen($digits) === 10) {
            return $digits;
        }

        return null;
    }

    protected function resolveStatus($rawStatus): int
    {
        if (empty($rawStatus)) {
            return $this->defaultStatusId;
        }
        $key = strtolower(trim((string)$rawStatus));
        return $this->leadStatuses[$key] ?? $this->defaultStatusId;
    }

    protected function resolveGeneralSource($rawSource): array
    {
        if (empty($rawSource)) {
            $directId = $this->leadSources['direct'] ?? reset($this->leadSources);
            return ['id' => $directId, 'is_expo' => false];
        }

        $key = strtolower(trim((string)$rawSource));
        if (str_contains($key, 'expo') || $key === 'expo') {
            return ['id' => $this->expoMasterSourceId, 'is_expo' => true];
        }

        if (isset($this->leadSources[$key])) {
            return ['id' => $this->leadSources[$key], 'is_expo' => false];
        }

        // Fuzzy search in sources
        foreach ($this->leadSources as $name => $id) {
            if (str_contains($key, $name) || str_contains($name, $key)) {
                return ['id' => $id, 'is_expo' => ($id === $this->expoMasterSourceId)];
            }
        }

        // Default to Direct
        $defaultId = $this->leadSources['direct'] ?? reset($this->leadSources);
        return ['id' => $defaultId, 'is_expo' => false];
    }

    protected function resolveProduct($rawProduct): ?int
    {
        if (empty($rawProduct)) {
            return null;
        }

        $val = trim((string)$rawProduct);

        // Check exact ID match
        if (isset($this->productsById[$val])) {
            return $this->productsById[$val];
        }

        $lower = strtolower($val);
        // Check exact product name match
        if (isset($this->productsByName[$lower])) {
            return $this->productsByName[$lower];
        }

        // Check exact product model match
        if (isset($this->productsByModel[$lower])) {
            return $this->productsByModel[$lower];
        }

        // Partial match
        foreach ($this->productsByName as $name => $id) {
            if (str_contains($name, $lower) || str_contains($lower, $name)) {
                return $id;
            }
        }

        return null;
    }

    protected function parseDate($cell, $rawValue): string
    {
        try {
            if ($cell && ExcelDate::isDateTime($cell)) {
                return ExcelDate::excelToDateTimeObject($cell->getValue())->format('Y-m-d');
            }
        } catch (Exception $e) {
            // continue
        }

        if (empty($rawValue)) {
            return date('Y-m-d');
        }

        $val = trim((string)$rawValue);

        // Check common date string formats
        $formats = ['d-m-Y', 'd/m/Y', 'Y-m-d', 'Y/m/d', 'm/d/Y', 'd.m.Y'];
        foreach ($formats as $fmt) {
            $d = \DateTime::createFromFormat($fmt, $val);
            if ($d && $d->format($fmt) === $val) {
                return $d->format('Y-m-d');
            }
        }

        $timestamp = strtotime($val);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return date('Y-m-d');
    }

    protected function resolveState($rawState): ?int
    {
        if (empty($rawState)) return null;
        $key = strtolower(trim((string)$rawState));
        return $this->statesByName[$key] ?? null;
    }

    protected function resolveCity($rawCity): ?int
    {
        if (empty($rawCity)) return null;
        $key = strtolower(trim((string)$rawCity));
        return $this->citiesByName[$key] ?? null;
    }

    protected function getCellValue($sheet, ?string $col, int $row)
    {
        if (!$col) return null;
        $cell = $sheet->getCell($col . $row);
        $val = $cell->getValue();
        if ($val instanceof RichText) {
            return $val->getPlainText();
        }
        return $val;
    }
}
