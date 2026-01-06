<?php

namespace Database\Seeders;

use App\Models\BulkDiscount;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $pants = Category::firstOrCreate(['slug' => 'pants'], ['name' => 'Pants', 'sort_order' => 1]);
            $washed = Category::firstOrCreate(['slug' => 'essential-washed-tank'], ['name' => 'ESSENTIAL washed tank', 'sort_order' => 2]);
            $tanks = Category::firstOrCreate(['slug' => 'essential-tanks'], ['name' => 'ESSENTIAL Tanks', 'sort_order' => 3]);
            $shirts = Category::firstOrCreate(['slug' => 't-shirts'], ['name' => 'T-Shirts', 'sort_order' => 4]);

            $imgs = [
                'image/WhatsApp Image 2025-03-21 at 20.06.30_1d88d07c.jpg',
                'image/WhatsApp Image 2025-12-16 at 09.39.35_47cefb4c.jpg',
                'image/WhatsApp Image 2025-12-16 at 09.39.36_6723b5aa.jpg',
                'image/WhatsApp Image 2025-12-17 at 08.11.24_498d41b4.jpg',
            ];

            $products = [
                [
                    'category_id' => $pants->id,
                    'name' => 'ESSENTIAL STRIPE SWEATPANTS',
                    'slug' => 'essential-stripe-sweatpants',
                    'price' => 810,
                    'compare_at_price' => 900,
                    'images' => [$imgs[0], $imgs[1]],
                    'variants' => [
                        ['Black', 'M', 8],
                        ['Black', 'L', 5],
                        ['Black', 'XL', 3],
                    ],
                ],
                [
                    'category_id' => $washed->id,
                    'name' => 'ESSENTIAL WASHED TANK',
                    'slug' => 'essential-washed-tank-product',
                    'price' => 450,
                    'compare_at_price' => 500,
                    'images' => [$imgs[1], $imgs[2]],
                    'variants' => [
                        ['Black', 'M', 10],
                        ['Black', 'L', 7],
                        ['Black', 'XL', 4],
                    ],
                ],
                [
                    'category_id' => $tanks->id,
                    'name' => 'ESSENTIAL COMPRESSION TANK',
                    'slug' => 'essential-compression-tank',
                    'price' => 292.5,
                    'compare_at_price' => 450,
                    'images' => [$imgs[2], $imgs[3]],
                    'variants' => [
                        ['White', 'M', 12],
                        ['White', 'L', 6],
                        ['Black', 'M', 9],
                    ],
                ],
                [
                    'category_id' => $shirts->id,
                    'name' => 'ESSENTIAL COMPRESSION T-SHIRT',
                    'slug' => 'essential-compression-tshirt',
                    'price' => 422.5,
                    'compare_at_price' => 650,
                    'images' => [$imgs[3], $imgs[0]],
                    'variants' => [
                        ['Black', 'M', 6],
                        ['Black', 'L', 4],
                        ['Red', 'M', 5],
                        ['White', 'M', 5],
                    ],
                ],
            ];

            foreach ($products as $p) {
                $product = Product::updateOrCreate(
                    ['slug' => $p['slug']],
                    [
                        'category_id' => $p['category_id'],
                        'name' => $p['name'],
                        'description' => 'وصف تجريبي للمنتج.',
                        'price' => $p['price'],
                        'compare_at_price' => $p['compare_at_price'],
                        'is_active' => true,
                    ]
                );

                ProductImage::where('product_id', $product->id)->delete();
                foreach ($p['images'] as $idx => $path) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => $path,
                        'sort_order' => $idx + 1,
                    ]);
                }

                ProductVariant::where('product_id', $product->id)->delete();
                foreach ($p['variants'] as [$color, $size, $qty]) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'color' => $color,
                        'size' => $size,
                        'quantity' => $qty,
                    ]);
                }

                BulkDiscount::where('product_id', $product->id)->delete();
                BulkDiscount::create([
                    'product_id' => $product->id,
                    'min_qty' => 2,
                    'discount_type' => 'percent',
                    'value' => 10,
                    'is_active' => true,
                ]);
            }
        });
    }
}

