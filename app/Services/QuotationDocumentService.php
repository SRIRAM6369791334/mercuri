<?php
namespace App\Services;

use App\Helpers\QuotationPresentationHelper;
use App\Models\Citylist;
use App\Models\Design;
use App\Models\FreightCategory;
use App\Models\FreightSubCategory;
use App\Models\ProductCategory;
use App\Models\Quotation;
use App\Models\QuotationInstallationScope;
use App\Models\QuotationOrcOption;
use App\Models\QuotationProduct;
use App\Models\QuotationUnloadingOption;
use App\Models\Statelist;

class QuotationDocumentService
{
    public function build(int $quotationId): array
    {
        $quotation = Quotation::with([
            'lead.assignedUser',
            'freight.termsCondition.product',
            'freight.warrantyTerm.product',
            'orfDetails.quopro',
        ])->findOrFail($quotationId);

        $quotationProducts = QuotationProduct::with([
            'productDetails.category',
            'productDetails.subcategory',
            'productDetails.specifications',
            'productDetails.uomData',
            'accessories',
        ])
            ->where('quotationno', $quotation->quota_id)
            ->get();

        $shippingState = $quotation->shipping_state
            ? Statelist::find($quotation->shipping_state)
            : null;

        $isTamilNadu = $shippingState
            && strtoupper(trim((string) $shippingState->state)) === 'TAMIL NADU';

        $leadCategory = ProductCategory::find($quotation->lead?->category);
        $isSpecial = $leadCategory?->sub_category === 'special';

        $design = $isSpecial
            ? Design::where('idlead', $quotation->id_lead)->first()
            : null;

        $billingCity = $quotation->billing_city ?? $quotation->lead?->citylist;
        $billingCityName = $billingCity
            ? optional(Citylist::find($billingCity))->city_name
            : null;

        $unloadingScope = $quotation->freight?->unloading
            ? optional(QuotationUnloadingOption::find($quotation->freight->unloading))->name
            : null;

        $productGroupImages = $quotationProducts
            ->mapWithKeys(function (QuotationProduct $item) {
                $product = $item->productDetails;
                return [$item->id => [
                    'name' => QuotationPresentationHelper::productGroupName($product),
                    'path' => QuotationPresentationHelper::productGroupImage($product),
                ]];
            });

        // ✅ Freight note lines for the commercial-offer "Notes" block
        $freight = $quotation->freight;
        $freightNoteLines = collect();

        if ($freight) {
            if ($freight->freight_category_id) {
                $freightNoteLines->push('Freight/Transportation: ' . optional(FreightCategory::find($freight->freight_category_id))->categoty);
            }
            if ($freight->freight_sub_category_id) {
                $freightNoteLines->push('Included or Extra: ' . optional(FreightSubCategory::find($freight->freight_sub_category_id))->sub_category);
            }
            if ($freight->unloading) {
                $freightNoteLines->push('Unloading at site: ' . optional(QuotationUnloadingOption::find($freight->unloading))->name);
            }
            if ($freight->installation) {
                $freightNoteLines->push('Installation Scope: ' . optional(QuotationInstallationScope::find($freight->installation))->name);
            }

            if ($quotation->additional_warranty_months > 0) {
                $freightNoteLines->push('Additional Warranty: ' . $quotation->additional_warranty_months . ' Months extended warranty (' . ($quotation->include_warranty_in_basic ? 'Included in Basic Price' : '₹' . number_format($quotation->additional_warranty_charge, 2)) . ')');
            }

            $freightNoteLines = $freightNoteLines->filter();
        }

        $freightCategoryName = $quotation->freight?->freight_category_id
            ? optional(FreightCategory::find($quotation->freight->freight_category_id))->categoty
            : 'Customer Scope';

        // Check if quotation is fully approved or in preview / pending status
        $termChangePending = \App\Models\QuotationTermChangeRequest::where('quotation_id', $quotation->id)
            ->where('status', 'Pending Approval')
            ->exists();
        $termChangeRejected = \App\Models\QuotationTermChangeRequest::where('quotation_id', $quotation->id)
            ->where('status', 'Rejected')
            ->exists();

        $termApprovePending = false;
        $termApproveRejected = false;
        if ($quotation->freight) {
            $termApprovePending = \App\Models\Term::where(function ($query) use ($quotation) {
                $query->where('id', $quotation->freight->termscondition)
                      ->orWhere('id', $quotation->freight->warranty);
            })->where('term_approve', 1)->exists();

            $termApproveRejected = \App\Models\Term::where(function ($query) use ($quotation) {
                $query->where('id', $quotation->freight->termscondition)
                      ->orWhere('id', $quotation->freight->warranty);
            })->where('term_approve', 2)->exists();
        }

        $chargePending = in_array($quotation->charge_approval_status, ['Pending Approval', 'Pending']);
        $chargeRejected = $quotation->charge_approval_status === 'Rejected';
        $discountPending = ($quotation->dis_app == 1);
        $discountRejected = ($quotation->dis_app === 0 || $quotation->dis_app === '0');
        $freightPending = ($quotation->freight_approval == 1);
        $freightRejected = ($quotation->freight_approval == 2);

        $hasPendingOrRejected = $termChangePending 
            || $termChangeRejected 
            || $termApprovePending 
            || $termApproveRejected 
            || $chargePending 
            || $chargeRejected 
            || $discountPending 
            || $discountRejected 
            || $freightPending 
            || $freightRejected;

        $isFullyApproved = ($quotation->quto_app == 2) && !$hasPendingOrRejected;
        $isPreview = !$isFullyApproved;

        return compact(
            'quotation',
            'quotationProducts',
            'shippingState',
            'isTamilNadu',
            'leadCategory',
            'isSpecial',
            'design',
            'billingCityName',
            'unloadingScope',
            'freightCategoryName',
            'productGroupImages',
            'freightNoteLines',
            'isPreview',
        );
    }
}