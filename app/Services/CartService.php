<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;

class CartService
{
    public function __construct(
        private readonly Session $session,
        private readonly PricingService $pricing,
    ) {
    }

    /**
     * @return array{items: array<int, array{key:string, product_id:int, variant_id:?int, qty:int}>, updated_at:int}
     */
    public function get(): array
    {
        return $this->session->get('cart', [
            'items' => [],
            'updated_at' => time(),
        ]);
    }

    public function clear(): void
    {
        $this->session->forget('cart');
    }

    public function add(int $productId, ?int $variantId, int $qty): void
    {
        $qty = max(1, (int) $qty);

        $cart = $this->get();
        $key = $this->key($productId, $variantId);

        $existingIndex = collect($cart['items'])->search(fn ($i) => ($i['key'] ?? null) === $key);
        if ($existingIndex !== false) {
            $cart['items'][(int) $existingIndex]['qty'] = max(1, (int) $cart['items'][(int) $existingIndex]['qty'] + $qty);
        } else {
            $cart['items'][] = [
                'key' => $key,
                'product_id' => $productId,
                'variant_id' => $variantId,
                'qty' => $qty,
            ];
        }

        $cart['updated_at'] = time();
        $this->session->put('cart', $cart);
    }

    public function update(string $key, int $qty): void
    {
        $qty = (int) $qty;

        $cart = $this->get();
        foreach ($cart['items'] as $idx => $item) {
            if (($item['key'] ?? null) !== $key) {
                continue;
            }

            if ($qty <= 0) {
                unset($cart['items'][$idx]);
            } else {
                $cart['items'][$idx]['qty'] = $qty;
            }
        }

        $cart['items'] = array_values($cart['items']);
        $cart['updated_at'] = time();
        $this->session->put('cart', $cart);
    }

    public function remove(string $key): void
    {
        $cart = $this->get();
        $cart['items'] = array_values(array_filter($cart['items'], fn ($i) => ($i['key'] ?? null) !== $key));
        $cart['updated_at'] = time();
        $this->session->put('cart', $cart);
    }

    /**
     * @return array{
     *   items: array<int, array{
     *     key:string, qty:int,
     *     product:Product,
     *     variant:?ProductVariant,
     *     unit_price:float,
     *     line_subtotal:float,
     *     line_discount:float,
     *     line_total:float,
     *     available_qty:int
     *   }>,
     *   subtotal:float,
     *   discount_total:float,
     *   total:float,
     *   count:int
     * }
     */
    public function hydrate(): array
    {
        $cart = $this->get();

        $items = collect($cart['items'] ?? [])
            ->filter(fn ($i) => isset($i['product_id'], $i['qty']))
            ->values();

        if ($items->isEmpty()) {
            return [
                'items' => [],
                'subtotal' => 0.0,
                'discount_total' => 0.0,
                'total' => 0.0,
                'count' => 0,
            ];
        }

        $productIds = $items->pluck('product_id')->unique()->values()->all();
        $variantIds = $items->pluck('variant_id')->filter()->unique()->values()->all();

        /** @var Collection<int, Product> $products */
        $products = Product::query()
            ->with(['images', 'bulkDiscounts'])
            ->whereIn('id', $productIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        /** @var Collection<int, ProductVariant> $variants */
        $variants = ProductVariant::query()
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        $hydrated = [];
        $subtotal = 0.0;
        $discountTotal = 0.0;
        $count = 0;

        foreach ($items as $item) {
            $product = $products->get((int) $item['product_id']);
            if (! $product) {
                continue;
            }

            $variant = null;
            if (! empty($item['variant_id'])) {
                $variant = $variants->get((int) $item['variant_id']);
                if (! $variant || $variant->product_id !== $product->id) {
                    continue;
                }
            }

            $qty = max(1, (int) $item['qty']);
            $unitPrice = (float) $product->price;
            $lineSubtotal = $unitPrice * $qty;
            $lineDiscount = $this->pricing->discountForProductQty($product, $qty, $unitPrice);
            $lineTotal = max(0.0, $lineSubtotal - $lineDiscount);
            $availableQty = $variant ? (int) $variant->quantity : 0;

            $hydrated[] = [
                'key' => (string) $item['key'],
                'qty' => $qty,
                'product' => $product,
                'variant' => $variant,
                'unit_price' => $unitPrice,
                'line_subtotal' => $lineSubtotal,
                'line_discount' => $lineDiscount,
                'line_total' => $lineTotal,
                'available_qty' => $availableQty,
            ];

            $subtotal += $lineSubtotal;
            $discountTotal += $lineDiscount;
            $count += $qty;
        }

        return [
            'items' => $hydrated,
            'subtotal' => round($subtotal, 2),
            'discount_total' => round($discountTotal, 2),
            'total' => round(max(0.0, $subtotal - $discountTotal), 2),
            'count' => $count,
        ];
    }

    public function key(int $productId, ?int $variantId): string
    {
        return $variantId ? "v:{$variantId}" : "p:{$productId}";
    }
}

