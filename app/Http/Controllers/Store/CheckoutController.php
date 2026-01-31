<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Services\CartService;
use App\Services\PromoCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function show(CartService $cart)
    {
        $hydrated = $cart->hydrate();
        if (count($hydrated['items']) === 0) {
            return redirect()->route('store.home');
        }

        return view('store.checkout', ['cart' => $hydrated]);
    }

    public function place(Request $request, CartService $cart, PromoCodeService $promoService)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'phone2' => ['nullable', 'string', 'max:30'],
            'governorate' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'promo_code' => ['nullable', 'string', 'max:50'],
        ]);

        $hydrated = $cart->hydrate();
        if (count($hydrated['items']) === 0) {
            return redirect()->route('store.home');
        }

        $order = DB::transaction(function () use ($data, $hydrated, $cart, $promoService) {
            $variantIds = collect($hydrated['items'])
                ->map(fn ($i) => $i['variant']?->id)
                ->filter()
                ->values()
                ->all();

            $variants = ProductVariant::query()
                ->whereIn('id', $variantIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $items = [];
            foreach ($hydrated['items'] as $cartItem) {
                $variant = $cartItem['variant'];
                if (! $variant) {
                    throw ValidationException::withMessages([
                        'cart' => 'لازم تختار لون ومقاس.',
                    ]);
                }

                $freshVariant = $variants->get($variant->id);
                if (! $freshVariant) {
                    throw ValidationException::withMessages([
                        'cart' => 'هذا الاختيار غير متاح.',
                    ]);
                }

                if ($freshVariant->quantity < $cartItem['qty']) {
                    throw ValidationException::withMessages([
                        'cart' => 'بعض المنتجات نفذت من المخزون. رجاءً حدّث السلة.',
                    ]);
                }
            }

            $promo = null;
            $promoDiscount = 0.0;
            $baseTotal = (float) ($hydrated['total'] ?? 0);

            $promoCodeInput = trim((string) ($data['promo_code'] ?? ''));
            if ($promoCodeInput !== '') {
                $promo = $promoService->findByCode($promoCodeInput, true);
                if (! $promo) {
                    throw ValidationException::withMessages([
                        'promo_code' => 'البروموكود غير صحيح.',
                    ]);
                }

                $promoService->assertCanApply($promo, $baseTotal);
                $promoDiscount = $promoService->discountFor($promo, $baseTotal);

                if ($promoDiscount <= 0) {
                    throw ValidationException::withMessages([
                        'promo_code' => 'البروموكود غير صالح.',
                    ]);
                }
            }

            $order = Order::create([
                'order_number' => 'MS-'.strtoupper(Str::random(8)),
                'status' => 'new',
                'customer_name' => $data['customer_name'],
                'phone' => $data['phone'],
                'phone2' => $data['phone2'] ?? null,
                'governorate' => $data['governorate'],
                'address' => $data['address'],
                'notes' => $data['notes'] ?? null,
                'subtotal' => $hydrated['subtotal'],
                'discount_total' => round(((float) $hydrated['discount_total']) + $promoDiscount, 2),
                'promo_code_id' => $promo?->id,
                'promo_code' => $promo?->code,
                'promo_discount' => round($promoDiscount, 2),
                'total' => round(max(0.0, ((float) $hydrated['total']) - $promoDiscount), 2),
                'payment_method' => 'cod',
            ]);

            foreach ($hydrated['items'] as $cartItem) {
                $product = $cartItem['product'];
                $variant = $variants->get($cartItem['variant']->id);

                $items[] = [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $product->name,
                    'color' => $variant->color,
                    'size' => $variant->size,
                    'unit_price' => $cartItem['unit_price'],
                    'qty' => $cartItem['qty'],
                    'line_subtotal' => $cartItem['line_subtotal'],
                    'line_discount' => $cartItem['line_discount'],
                    'line_total' => $cartItem['line_total'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $variant->decrement('quantity', $cartItem['qty']);
            }

            OrderItem::insert($items);

            if ($promo) {
                $promo->increment('used_count');
            }

            $cart->clear();

            return $order;
        });

        return redirect()->route('store.order.success', $order);
    }

    public function success(Order $order)
    {
        return view('store.success', ['order' => $order]);
    }
}
