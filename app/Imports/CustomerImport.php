<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use App\Models\Dropdowndata;

class CustomerImport implements ToModel, WithHeadingRow
{
    private $currentId;
    private $rowNumber = 1;
    public $importedCount = 0;
    public $skippedCount = 0;
    public $errors = [];

    // Form IDs for dropdown tables
    const DESIGNATION_FORM_ID = 80;
    const DEPARTMENT_FORM_ID  = 81;

    public function __construct()
    {
        $lastCustomer = Customer::orderBy('id', 'desc')->first();
        $lastId = $lastCustomer ? intval(substr($lastCustomer->cusid, 3)) : 0;
        $this->currentId = $lastId;
    }

    public function model(array $row)
    {
        $this->rowNumber++;
        $rowNum = $this->rowNumber;

        // 10 Mandatory fields
        $customerName = trim($row['customer_name'] ?? $row['customername'] ?? $row['customer'] ?? '');
        $gstNo        = strtoupper(trim($row['gst_no'] ?? $row['gstno'] ?? $row['gst'] ?? ''));
        $contactName  = trim($row['contact_name'] ?? $row['contactname'] ?? $row['contact_person'] ?? '');
        $contactNo    = trim($row['contact_no'] ?? $row['contactno'] ?? $row['contact_number'] ?? $row['phone'] ?? $row['mobile'] ?? '');
        $cusEmail     = trim($row['email'] ?? $row['cus_email'] ?? $row['cusemail'] ?? $row['customer_email'] ?? '');
        $address      = trim($row['billing_address'] ?? $row['address'] ?? $row['bill_address'] ?? '');
        $countryVal   = trim($row['country'] ?? '');
        $stateVal     = trim($row['state'] ?? '');
        $cityVal      = trim($row['city'] ?? '');
        $postalCode   = trim($row['postal_code'] ?? $row['postalcode'] ?? $row['pincode'] ?? $row['pin_code'] ?? '');

        // 4 Optional fields
        $commAddress     = trim($row['communication_address'] ?? $row['comm_address'] ?? $row['shipping_address'] ?? '');
        $assignedUserVal = trim($row['assigned_user'] ?? $row['assigneduser'] ?? $row['user'] ?? '');
        $designation     = trim($row['designation'] ?? '');
        $department      = trim($row['department'] ?? '');

        // Check if completely empty row (e.g. trailing empty rows in spreadsheet)
        if (empty($customerName) && empty($gstNo) && empty($contactName) && empty($contactNo) && empty($cusEmail) && empty($address)) {
            return null;
        }

        // Validate 10 Mandatory Fields
        $missingFields = [];
        if (empty($customerName)) {
            $missingFields[] = 'Customer Name';
        }
        if (empty($gstNo)) {
            $missingFields[] = 'GST No';
        }
        if (empty($contactName)) {
            $missingFields[] = 'Contact Name';
        }
        if (empty($contactNo)) {
            $missingFields[] = 'Contact No';
        }
        if (empty($cusEmail)) {
            $missingFields[] = 'Email';
        }
        if (empty($address)) {
            $missingFields[] = 'Billing Address';
        }
        if (empty($countryVal)) {
            $missingFields[] = 'Country';
        }
        if (empty($stateVal)) {
            $missingFields[] = 'State';
        }
        if (empty($cityVal)) {
            $missingFields[] = 'City';
        }
        if (empty($postalCode)) {
            $missingFields[] = 'Postal Code';
        }

        if (!empty($missingFields)) {
            $this->skippedCount++;
            $this->errors[] = "Row {$rowNum}: Missing mandatory field(s): " . implode(', ', $missingFields) . ".";
            return null;
        }

        // Validate Country
        $countryName = strtolower(str_replace(' ', '', $countryVal));
        $country = DB::table('countries')->whereRaw("REPLACE(LOWER(name), ' ', '') LIKE ?", ['%' . $countryName . '%'])->first();
        $countryId = $country ? $country->id : 76;

        // Validate State
        $stateName = strtolower(str_replace(' ', '', $stateVal));
        $state = DB::table('state_list')->whereRaw("REPLACE(LOWER(state), ' ', '') LIKE ?", ['%' . $stateName . '%'])->first();
        if (!$state) {
            $this->skippedCount++;
            $this->errors[] = "Row {$rowNum}: State '{$stateVal}' was not found in state list.";
            return null;
        }
        $stateId = $state->id;

        // Validate City
        $cityName = strtolower(str_replace(' ', '', $cityVal));
        $city = DB::table('city_list')->whereRaw("REPLACE(LOWER(city_name), ' ', '') LIKE ?", ['%' . $cityName . '%'])->first();
        if (!$city) {
            $this->skippedCount++;
            $this->errors[] = "Row {$rowNum}: City '{$cityVal}' was not found in city list.";
            return null;
        }
        $cityId = $city->id;

        // Prevent duplicate customer import by GST + Pincode or Name + Pincode
        $existing = Customer::where('soft_d', 0)
            ->whereNotNull('gstno')
            ->where('gstno', '!=', '')
            ->whereRaw('UPPER(TRIM(gstno)) = ?', [$gstNo])
            ->whereNotNull('postalcode')
            ->where('postalcode', '!=', '')
            ->whereRaw('TRIM(postalcode) = ?', [$postalCode])
            ->first();

        if (!$existing) {
            $existing = Customer::where('soft_d', 0)
                ->whereRaw('LOWER(TRIM(customername)) = ?', [strtolower($customerName)])
                ->whereNotNull('postalcode')
                ->where('postalcode', '!=', '')
                ->whereRaw('TRIM(postalcode) = ?', [$postalCode])
                ->first();
        }

        if ($existing) {
            $this->skippedCount++;
            $this->errors[] = "Row {$rowNum}: Customer already exists with matching GST/Name and Postal Code ({$existing->cusid}).";
            return null;
        }

        // Optional: Communication address falls back to Billing address if empty
        if (empty($commAddress)) {
            $commAddress = $address;
        }

        // Optional: Resolve Assigned User (by ID, Name, or Email)
        $assignedUserId = null;
        if (!empty($assignedUserVal)) {
            $user = \App\Models\User::where('id', $assignedUserVal)
                ->orWhereRaw('LOWER(TRIM(name)) = ?', [strtolower($assignedUserVal)])
                ->orWhereRaw('LOWER(TRIM(email)) = ?', [strtolower($assignedUserVal)])
                ->first();
            $assignedUserId = $user ? $user->id : null;
        }

        // Optional: Get or create dropdown IDs for designation and department
        $designationId = $this->getOrCreateDropdownId($designation, self::DESIGNATION_FORM_ID);
        $departmentId  = $this->getOrCreateDropdownId($department, self::DEPARTMENT_FORM_ID);

        $this->currentId++;
        $this->importedCount++;

        return new Customer([
            "cusid"                 => 'CUS' . str_pad($this->currentId, 4, '0', STR_PAD_LEFT),
            "customername"          => $customerName,
            "gstno"                 => $gstNo,
            "address"               => $address,
            "communication_address" => $commAddress,
            "country_code"          => $countryId,
            "country"               => $countryId,
            "state"                 => $stateId,
            "city"                  => $cityId,
            "postalcode"            => $postalCode,
            "contact_name"          => $contactName,
            "cus_email"             => $cusEmail,
            "contact_no"            => $contactNo,
            "assigned_user"         => $assignedUserId,
            "designation"           => $designationId,
            "department"            => $departmentId,
        ]);
    }

    private function getOrCreateDropdownId($value, $formId)
    {
        if (empty($value)) {
            return null;
        }

        $value = trim($value);

        // Check if already exists (case-insensitive)
        $existing = Dropdowndata::where('formid', $formId)
            ->whereRaw('LOWER(dropdowndata) = ?', [strtolower($value)])
            ->first();

        if ($existing) {
            return $existing->id; // ✅ match found → return existing id
        }

        // Not found → insert new record and return its id
        $orderNo = Dropdowndata::where('formid', $formId)->max('orderno') + 1;

        return Dropdowndata::create([
            'formid'       => $formId,
            'dropdowndata' => $value,
            'orderno'      => $orderNo,
        ])->id; // ✅ new record → return new id
    }
}
