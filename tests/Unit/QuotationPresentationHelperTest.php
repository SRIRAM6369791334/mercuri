<?php

namespace Tests\Unit;

use App\Helpers\QuotationPresentationHelper;
use App\Models\Product;
use App\Models\ProductSubCategory;
use PHPUnit\Framework\TestCase;

class QuotationPresentationHelperTest extends TestCase
{
    /** @dataProvider groupImageProvider */
    public function test_it_resolves_the_supplied_image_for_each_product_group(
        string $subCategory,
        string $productName,
        string $expectedFile
    ): void {
        $product = new Product(['productname' => $productName]);
        $product->setRelation('subcategory', new ProductSubCategory([
            'subcategory_name' => $subCategory,
        ]));

        $this->assertStringEndsWith(
            $expectedFile,
            QuotationPresentationHelper::productGroupImage($product)
        );
    }

    public static function groupImageProvider(): array
    {
        return [
            ['Hydraulic Hand Pallet Truck', 'PV25E', 'hydraulic-hand-pallet-truck.png'],
            ['Electric Stacker - Ride on', 'ST15 FFTL', 'electric-stacker.png'],
            ['Dock Leveler', 'Dock Leveler 9000 Kg', 'dock-leveler.png'],
            ['Electric Pallet Truck - Ride on', 'SP20', 'electric-pallet-truck.png'],
            ['Electric ForkLift', 'FB20R', 'electric-forklift.png'],
            ['Reach Truck', 'CQD20D Reach Truck', 'reach-truck-cqd20.png'],
            ['Reach Truck', 'CQD15 Reach Truck', 'reach-truck-cqd15.png'],
            ['Tow Truck', 'TT-20', 'tow-truck.png'],
            ['Buggy', 'Battery Operated 6 Seater', 'buggy.png'],
        ];
    }

    public function test_it_prefers_the_uploaded_product_group_image(): void
    {
        $product = new Product(['productname' => 'PV25E']);
        $product->setRelation('subcategory', new ProductSubCategory([
            'subcategory_name' => 'Hydraulic Hand Pallet Truck',
            'quotation_image' => 'quotation-group-images/custom.webp',
        ]));

        $this->assertSame(
            'storage/quotation-group-images/custom.webp',
            QuotationPresentationHelper::productGroupImage($product),
        );
    }

    public function test_it_formats_product_description_with_group_and_model(): void
    {
       
        $product1 = new Product(['productmodel' => '4 Seater-Lead Acid', 'productname' => 'Battery Operated 4 Seater']);
        $product1->setRelation('subcategory', new ProductSubCategory(['subcategory_name' => 'Buggy']));
        $this->assertSame('Buggy - 4 Seater-Lead Acid', QuotationPresentationHelper::productDescription($product1));

        
        $product2 = new Product(['productmodel' => 'Buggy 6 Seater', 'productname' => 'Maini Buggy']);
        $product2->setRelation('subcategory', new ProductSubCategory(['subcategory_name' => 'Buggy']));
        $this->assertSame('Buggy 6 Seater', QuotationPresentationHelper::productDescription($product2));

      
        $product3 = new Product(['productmodel' => 'PV25E - PU']);
        $this->assertSame('PV25E - PU', QuotationPresentationHelper::productDescription($product3));

    
        $product4 = new Product(['productname' => 'Custom Equipment']);
        $product4->setRelation('subcategory', new ProductSubCategory(['subcategory_name' => 'Hydraulic Hand Pallet Truck']));
        $this->assertSame('Hydraulic Hand Pallet Truck', QuotationPresentationHelper::productDescription($product4));

       
        $this->assertSame('Product', QuotationPresentationHelper::productDescription(null));

       
        $product5 = new Product([
            'productname' => 'Maini Make-Hydraulic Hand Pallet Truck - PV25E(1150X520mm) - PU Wheel',
            'productmodel' => 'PV25E - PU',
        ]);
        $this->assertSame('Hydraulic Hand Pallet Truck - PV25E - PU', QuotationPresentationHelper::productDescription($product5));
    }
}
