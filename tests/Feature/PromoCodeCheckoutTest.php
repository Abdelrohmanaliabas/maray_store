<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\PromoCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromoCodeCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite extension is not installed.');
        }

        parent::setUp();
    }

    public function test_checkout_applies_promo_code_and_increments_usage(): void
    {
        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 100,
            'is_active' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'color' => 'Black',
            'size' => 'M',
            'quantity' => 10,
        ]);

        $promo = PromoCode::create([
            'code' => 'SAVE10',
            'discount_type' => 'percent',
            'value' => 10,
            'min_order_total' => 0,
            'usage_limit' => 5,
            'used_count' => 0,
            'is_active' => true,
        ]);

        $this->withoutMiddleware();

        $response = $this
            ->withSession([
                'cart' => [
                    'items' => [
                        [
                            'key' => "v:{$variant->id}",
                            'product_id' => $product->id,
                            'variant_id' => $variant->id,
                            'qty' => 1,
                        ],
                    ],
                    'updated_at' => time(),
                ],
            ])
            ->post('/checkout', [
                'customer_name' => 'Customer',
                'phone' => '01000000000',
                'phone2' => null,
                'governorate' => 'Cairo',
                'address' => 'Street 1',
                'notes' => null,
                'promo_code' => 'save10',
            ]);

        $response->assertRedirect();

        $order = Order::query()->firstOrFail();
        $this->assertSame($promo->id, $order->promo_code_id);
        $this->assertSame('SAVE10', $order->promo_code);
        $this->assertSame('10.00', (string) $order->promo_discount);
        $this->assertSame('10.00', (string) $order->discount_total);
        $this->assertSame('90.00', (string) $order->total);

        $this->assertDatabaseHas('promo_codes', [
            'id' => $promo->id,
            'used_count' => 1,
        ]);

        $this->assertDatabaseHas('product_variants', [
            'id' => $variant->id,
            'quantity' => 9,
        ]);
    }
}
