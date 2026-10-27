<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class ProductImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        $catName = strtolower(str_replace(' ', '', $row['productcategory']));

        $catData = DB::table('product_categories_tb')->whereRaw("REPLACE(LOWER(category_name), ' ', '') LIKE ?", [$catName])->first();

        $catId = $catData->id ?? null;

        return new Product([
            "productname" => $row['productname'],
            "productcategory" => $catId,
            "gst" => $row['gst'],
            "partno" => $row['partno'],
            "hsn" => $row['hsn'],
            "rate" => preg_replace('/[^\d.]/', '', (string) ($row['rate'] ?? 0)),
            "quantity" => $row['qty'],
            "productdes" => $row['productdescription']
        ]);
    }
}
