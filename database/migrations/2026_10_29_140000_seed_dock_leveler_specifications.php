<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $products = [
            39 => ['width' => 2000, 'length' => 2500, 'height' => 480],
            40 => ['width' => 2000, 'length' => 2500, 'height' => 550],
            42 => ['width' => 2130, 'length' => 2730, 'height' => 480],
            43 => ['width' => 2000, 'length' => 2730, 'height' => 480],
        ];

        $template = DB::table('product_specifications')->where('productid', 41)->get();

        if ($template->isEmpty()) {
            return;
        }

        foreach ($products as $productId => $dims) {
            $existingCount = DB::table('product_specifications')->where('productid', $productId)->count();
            if ($existingCount > 0) {
                continue;
            }

            foreach ($template as $t) {
                $val = $t->specification;
                if ($t->description === 'Deck Width') {
                    $val = (string) $dims['width'];
                } elseif ($t->description === 'Deck Length') {
                    $val = (string) $dims['length'];
                } elseif ($t->description === 'Deck Height') {
                    $val = (string) $dims['height'];
                }

                DB::table('product_specifications')->insert([
                    'productid'     => $productId,
                    'description'   => $t->description,
                    'unit'          => $t->unit,
                    'specification' => $val,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('product_specifications')
            ->whereIn('productid', [39, 40, 42, 43])
            ->delete();
    }
};
