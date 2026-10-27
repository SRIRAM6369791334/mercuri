<?php

namespace App\Helpers;

use App\Models\Product;

class QuotationPresentationHelper
{
    /**
     * Resolve the feature image supplied for a quotation product group.
     *
     * The sub-category is the primary source of truth. Product-name checks
     * cover older product rows whose sub-category was not populated.
     */
    public static function productGroupImage(?Product $product): ?string
    {
        if (! $product) {
            return null;
        }

        $subCategoryModel = $product->subcategory;
        $customImage = trim((string) optional($subCategoryModel)->quotation_image);

        if ($customImage !== '') {
            $publicPath = ltrim($customImage, '/');

            if (str_starts_with($publicPath, 'quotation-group-images/')) {
                return 'storage/'.$publicPath;
            }

            if (! str_starts_with($publicPath, 'storage/')
                && ! str_starts_with($publicPath, 'uploads/')) {
                $publicPath = 'uploads/'.$publicPath;
            }

            return $publicPath;
        }

        return self::defaultProductGroupImage(
            (string) optional($subCategoryModel)->subcategory_name,
            (string) $product->productname,
        );
    }

    public static function defaultProductGroupImage(string $subCategory, string $productName = ''): ?string
    {
        $subCategory = strtolower(trim($subCategory));
        $productName = strtolower(trim($productName));
        $group = trim($subCategory.' '.$productName);

        if (str_contains($group, 'hydraulic') && str_contains($group, 'pallet truck')) {
            return 'assets/images/quotation-groups/hydraulic-hand-pallet-truck.png';
        }

        if (str_contains($group, 'dock leveler') || str_contains($group, 'docl leveler')) {
            return 'assets/images/quotation-groups/dock-leveler.png';
        }

        if (str_contains($group, 'forklift') || str_contains($group, 'fork lift')) {
            return 'assets/images/quotation-groups/electric-forklift.png';
        }

        if (str_contains($group, 'reach truck')) {
            return str_contains($productName, 'cqd20')
                ? 'assets/images/quotation-groups/reach-truck-cqd20.png'
                : 'assets/images/quotation-groups/reach-truck-cqd15.png';
        }

        if (str_contains($group, 'tow truck') || str_contains($group, 'trolley tug')) {
            return 'assets/images/quotation-groups/tow-truck.png';
        }

        if (str_contains($group, 'buggy') || (str_contains($group, 'battery operated') && str_contains($group, 'seater'))) {
            return 'assets/images/quotation-groups/buggy.png';
        }

        if (str_contains($group, 'stacker')) {
            return 'assets/images/quotation-groups/electric-stacker.png';
        }

        if (str_contains($group, 'pallet truck')) {
            return 'assets/images/quotation-groups/electric-pallet-truck.png';
        }

        return null;
    }

    public static function productGroupName(?Product $product): string
    {
        $subCategoryModel = ($product?->relationLoaded('subcategory') || $product?->exists)
            ? $product->subcategory
            : null;
        $subCategory = trim((string) optional($subCategoryModel)->subcategory_name);

        if ($subCategory !== '') {
            return $subCategory;
        }

        $rawName = trim((string) ($product?->productname ?? ''));
        if ($rawName === '') {
            return 'Product';
        }

        if (str_contains($rawName, ' - ')) {
            $parts = explode(' - ', $rawName);
            $prefix = trim($parts[0]);
            if (!empty($prefix)) {
                return $prefix;
            }
        }

        return $rawName;
    }

    public static function productDescription(?Product $product): string
    {
        if (! $product) {
            return 'Product';
        }

        $group = self::productGroupName($product);
        if ($group === 'Product') {
            $group = '';
        }
        $cleanGroup = preg_replace('/^Maini\s*Make\s*[-–—]?\s*/i', '', $group);
        if (!empty($cleanGroup)) {
            $group = $cleanGroup;
        }

        $model = trim((string) ($product->productmodel ?? ''));

        if ($group !== '' && $model !== '') {
            if (str_contains(strtolower($model), strtolower($group))) {
                return $model;
            }

            return $group . ' - ' . $model;
        }

        if ($model !== '') {
            return $model;
        }

        if ($group !== '') {
            return $group;
        }

        $rawName = trim((string) ($product->productname ?? ''));

        return $rawName !== '' ? $rawName : 'Product';
    }
}