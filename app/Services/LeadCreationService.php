<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Dropdowndata;
use App\Models\Lead;
use App\Models\LeadProduct;
use Illuminate\Support\Facades\DB;

class LeadCreationService
{
    /**
     * Create a General Lead (and optionally link/create Customer if WON).
     */
    public function createGeneralLead(array $data, ?int $userId = null): Lead
    {
        $userId = $userId ?? 1;

        return DB::transaction(function () use ($data, $userId) {
            $lead = new Lead();
            $lead->LeadName = $data['lead_name'] ?? $data['company_name'] ?? $data['name'] ?? null;
            $lead->contactname = $data['contact_name'] ?? $data['name'] ?? null;
            $lead->phonumber = $data['phone'] ?? null;
            $lead->email = $data['email'] ?? null;
            $lead->billaddress = $data['address'] ?? null;
            $lead->state_id = !empty($data['state_id']) ? $data['state_id'] : null;
            $lead->citylist = !empty($data['city_id']) ? $data['city_id'] : null;
            $lead->Leadsource = $data['lead_source_id'] ?? null;
            $lead->Leadstatus = $data['lead_status_id'] ?? null;
            $lead->Entrydate = $data['entry_date'] ?? date('Y-m-d');
            $lead->enquirydetail = $data['remarks'] ?? $data['enquirydetail'] ?? null;
            $lead->remarks = $data['remarks'] ?? null;
            $lead->gst = $data['gst'] ?? null;
            $lead->assigned_to = $userId;
            $lead->customeriid = $data['customer_id'] ?? null;
            $lead->soft_delete = 0;
            $lead->save();

            // Set formatted lead_id
            $lead->lead_id = $this->generateLeadId($lead->id);
            $lead->save();

            // Insert into leadproducts
            DB::table('leadproducts')->insert([
                'leadid' => $lead->id,
                'singleproduct' => !empty($data['product_id']) ? (string)$data['product_id'] : null,
                'statuslead' => $lead->Leadstatus,
                'enquirydate' => $lead->Entrydate,
                'totalcost' => isset($data['deal_value']) && is_numeric($data['deal_value']) ? $data['deal_value'] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // If status is WON (343), ensure customer exists
            if ((string)$lead->Leadstatus === '343' && empty($lead->customeriid)) {
                $customer = Customer::where('contact_no', $lead->phonumber)
                    ->where('soft_d', 0)
                    ->first();

                if (!$customer) {
                    $customer = new Customer();
                    $customer->customername = $lead->LeadName ?: $lead->contactname;
                    $customer->contact_name = $lead->contactname ?: $lead->LeadName;
                    $customer->contact_no = $lead->phonumber;
                    $customer->cus_email = $lead->email;
                    $customer->address = $lead->billaddress;
                    $customer->state = $lead->state_id;
                    $customer->city = $lead->citylist;
                    $customer->gstno = $lead->gst;
                    $customer->assigned_user = $userId;
                    $customer->soft_d = 0;
                    $customer->save();

                    $customer->cusid = $this->generateCustomerId($customer->id);
                    $customer->save();
                }

                $lead->customeriid = $customer->id;
                $lead->save();
            }

            return $lead;
        });
    }

    /**
     * Create an Expo Lead:
     * Guarantees a Customer is linked or created with CUSxxxx,
     * Lead is assigned Expo master Leadsource, and LeadProduct record is created.
     */
    public function createExpoLead(array $data, ?int $userId = null): Lead
    {
        $userId = $userId ?? 1;

        return DB::transaction(function () use ($data, $userId) {
            $phone = $data['phone'] ?? null;
            $customerName = $data['company_name'] ?? $data['lead_name'] ?? $data['name'] ?? null;
            $contactName = $data['contact_name'] ?? $data['name'] ?? $customerName;

            // 1. Find or create Customer
            $customer = null;
            if (!empty($phone)) {
                $customer = Customer::where('contact_no', $phone)
                    ->where('soft_d', 0)
                    ->first();
            }

            if (!$customer) {
                $customer = new Customer();
                $customer->customername = $customerName ?: $contactName;
                $customer->contact_name = $contactName ?: $customerName;
                $customer->contact_no = $phone;
                $customer->cus_email = $data['email'] ?? null;
                $customer->address = $data['address'] ?? null;
                $customer->state = !empty($data['state_id']) ? $data['state_id'] : null;
                $customer->city = !empty($data['city_id']) ? $data['city_id'] : null;
                $customer->gstno = $data['gst'] ?? null;
                $customer->assigned_user = $userId;
                $customer->soft_d = 0;
                $customer->save();

                $customer->cusid = $this->generateCustomerId($customer->id);
                $customer->save();
            } else {
                // If customer exists, fill any missing fields
                $dirty = false;
                if (empty($customer->cus_email) && !empty($data['email'])) {
                    $customer->cus_email = $data['email'];
                    $dirty = true;
                }
                if (empty($customer->address) && !empty($data['address'])) {
                    $customer->address = $data['address'];
                    $dirty = true;
                }
                if (empty($customer->state) && !empty($data['state_id'])) {
                    $customer->state = $data['state_id'];
                    $dirty = true;
                }
                if (empty($customer->city) && !empty($data['city_id'])) {
                    $customer->city = $data['city_id'];
                    $dirty = true;
                }
                if ($dirty) {
                    $customer->save();
                }
            }

            // 2. Prepare Expo Details & Remarks
            $expoEvent = trim($data['expo_event'] ?? '');
            $rawRemarks = trim($data['remarks'] ?? '');
            $fullRemarks = $rawRemarks;
            if (!empty($expoEvent)) {
                $fullRemarks = !empty($rawRemarks)
                    ? "[Expo: {$expoEvent}] " . $rawRemarks
                    : "[Expo: {$expoEvent}]";
            }

            // 3. Create Lead
            $lead = new Lead();
            $lead->customeriid = $customer->id;
            $lead->LeadName = $customer->customername;
            $lead->contactname = $customer->contact_name ?: $contactName;
            $lead->phonumber = $customer->contact_no ?: $phone;
            $lead->email = $customer->cus_email ?: ($data['email'] ?? null);
            $lead->billaddress = $data['address'] ?? $customer->address;
            $lead->state_id = !empty($data['state_id']) ? $data['state_id'] : $customer->state;
            $lead->citylist = !empty($data['city_id']) ? $data['city_id'] : $customer->city;
            $lead->Leadsource = $data['lead_source_id']; // Master Expo source ID (391)
            $lead->Leadstatus = $data['lead_status_id'] ?? 340; // Default COLD (340) or mapped status
            $lead->Entrydate = $data['entry_date'] ?? date('Y-m-d');
            $lead->enquirydetail = $fullRemarks;
            $lead->remarks = $fullRemarks;
            $lead->assigned_to = $userId;
            $lead->soft_delete = 0;
            $lead->save();

            $lead->lead_id = $this->generateLeadId($lead->id);
            $lead->save();

            // 4. Create LeadProduct
            DB::table('leadproducts')->insert([
                'leadid' => $lead->id,
                'singleproduct' => !empty($data['product_id']) ? (string)$data['product_id'] : null,
                'statuslead' => $lead->Leadstatus,
                'enquirydate' => $lead->Entrydate,
                'totalcost' => isset($data['deal_value']) && is_numeric($data['deal_value']) ? $data['deal_value'] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $lead;
        });
    }

    /**
     * Update existing Lead and linked Customer (fill empty fields only; never overwrite non-empty with empty).
     */
    public function updateExistingLead(Lead $lead, array $data, string $type = 'general'): Lead
    {
        return DB::transaction(function () use ($lead, $data, $type) {
            $leadDirty = false;

            if (empty($lead->contactname) && !empty($data['name'])) {
                $lead->contactname = $data['name'];
                $leadDirty = true;
            }
            if (empty($lead->LeadName) && !empty($data['company_name'])) {
                $lead->LeadName = $data['company_name'];
                $leadDirty = true;
            }
            if (empty($lead->email) && !empty($data['email'])) {
                $lead->email = $data['email'];
                $leadDirty = true;
            }
            if (empty($lead->billaddress) && !empty($data['address'])) {
                $lead->billaddress = $data['address'];
                $leadDirty = true;
            }
            if (empty($lead->state_id) && !empty($data['state_id'])) {
                $lead->state_id = $data['state_id'];
                $leadDirty = true;
            }
            if (empty($lead->citylist) && !empty($data['city_id'])) {
                $lead->citylist = $data['city_id'];
                $leadDirty = true;
            }
            if (empty($lead->gst) && !empty($data['gst'])) {
                $lead->gst = $data['gst'];
                $leadDirty = true;
            }

            // Append remarks if new content provided
            $newRemarks = trim($data['remarks'] ?? '');
            if (!empty($newRemarks) && stripos((string)$lead->remarks, $newRemarks) === false) {
                $lead->remarks = trim($lead->remarks . "\n[Import Update " . date('Y-m-d') . "] " . $newRemarks);
                $lead->enquirydetail = $lead->remarks;
                $leadDirty = true;
            }

            if ($leadDirty) {
                $lead->save();
            }

            // Update linked Customer if exists
            if (!empty($lead->customeriid)) {
                $customer = Customer::find($lead->customeriid);
                if ($customer) {
                    $cusDirty = false;
                    if (empty($customer->cus_email) && !empty($lead->email)) {
                        $customer->cus_email = $lead->email;
                        $cusDirty = true;
                    }
                    if (empty($customer->address) && !empty($lead->billaddress)) {
                        $customer->address = $lead->billaddress;
                        $cusDirty = true;
                    }
                    if (empty($customer->state) && !empty($lead->state_id)) {
                        $customer->state = $lead->state_id;
                        $cusDirty = true;
                    }
                    if (empty($customer->city) && !empty($lead->citylist)) {
                        $customer->city = $lead->citylist;
                        $cusDirty = true;
                    }
                    if (empty($customer->gstno) && !empty($lead->gst)) {
                        $customer->gstno = $lead->gst;
                        $cusDirty = true;
                    }
                    if ($cusDirty) {
                        $customer->save();
                    }
                }
            }

            // If product was provided and existing lead product has null singleproduct, update it
            if (!empty($data['product_id'])) {
                DB::table('leadproducts')
                    ->where('leadid', $lead->id)
                    ->whereNull('singleproduct')
                    ->update(['singleproduct' => (string)$data['product_id']]);
            }

            return $lead;
        });
    }

    public function generateLeadId(int $id): string
    {
        return 'LEAD' . str_pad($id, 4, '0', STR_PAD_LEFT);
    }

    public function generateCustomerId(int $id): string
    {
        return 'CUS' . str_pad($id, 4, '0', STR_PAD_LEFT);
    }
}
