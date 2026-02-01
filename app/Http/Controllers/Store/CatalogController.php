<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function home(Request $request)
    {
        $query = Product::query()
            ->where('is_active', true)
            ->with([
                'images',
                'category',
                'bulkDiscounts' => fn ($q) => $q->where('is_active', true)->orderByDesc('min_qty'),
            ])
            ->withCount(['variants as in_stock_variants_count' => function ($q) {
                $q->where('quantity', '>', 0);
            }])
            ->orderByDesc('id');

        if ($request->filled('q')) {
            $q = trim((string) $request->get('q'));
            $query->where('name', 'like', "%{$q}%");
        }

        $products = $this->applySort($query, $request)->paginate(12)->withQueryString();

        return view('store.home', [
            'categories' => Category::orderBy('sort_order')->orderBy('name')->get(),
            'products' => $products,
        ]);
    }

    public function category(Request $request, Category $category)
    {
        $query = $category->products()
            ->where('is_active', true)
            ->with([
                'images',
                'category',
                'bulkDiscounts' => fn ($q) => $q->where('is_active', true)->orderByDesc('min_qty'),
            ])
            ->withCount(['variants as in_stock_variants_count' => function ($q) {
                $q->where('quantity', '>', 0);
            }])
            ->orderByDesc('id');

        if ($request->filled('q')) {
            $q = trim((string) $request->get('q'));
            $query->where('name', 'like', "%{$q}%");
        }

        $products = $this->applySort($query, $request)->paginate(12)->withQueryString();

        return view('store.category', [
            'category' => $category,
            'products' => $products,
        ]);
    }

    public function product(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load(['images', 'variants', 'bulkDiscounts', 'category']);

        $limit = 8;
        $sameCategory = Product::query()
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->with([
                'images',
                'category',
                'bulkDiscounts' => fn ($q) => $q->where('is_active', true)->orderByDesc('min_qty'),
            ])
            ->withCount(['variants as in_stock_variants_count' => function ($q) {
                $q->where('quantity', '>', 0);
            }])
            ->orderByDesc('id')
            ->take($limit)
            ->get();

        $remaining = max(0, $limit - $sameCategory->count());
        $otherProducts = collect();
        if ($remaining > 0) {
            $otherProducts = Product::query()
                ->where('is_active', true)
                ->where('id', '!=', $product->id)
                ->when($product->category_id, fn ($q) => $q->where('category_id', '!=', $product->category_id))
                ->with([
                    'images',
                    'category',
                    'bulkDiscounts' => fn ($q) => $q->where('is_active', true)->orderByDesc('min_qty'),
                ])
                ->withCount(['variants as in_stock_variants_count' => function ($q) {
                    $q->where('quantity', '>', 0);
                }])
                ->orderByDesc('id')
                ->take($remaining)
                ->get();
        }

        return view('store.product', [
            'product' => $product,
            'relatedProducts' => $sameCategory->concat($otherProducts),
        ]);
    }

    public function variants(Product $product)
    {
        abort_unless($product->is_active, 404);

        $variants = $product->variants()
            ->select(['id', 'color', 'size', 'quantity'])
            ->where('quantity', '>', 0)
            ->get();

        $colors = $variants->groupBy('color')->map(function ($group) {
            return $group->map(fn ($v) => [
                'id' => $v->id,
                'size' => $v->size,
                'qty' => (int) $v->quantity,
            ])->values();
        });

        return response()->json([
            'product_id' => $product->id,
            'colors' => $colors,
        ]);
    }

    private function applySort($query, Request $request)
    {
        return match ($request->string('sort')->toString()) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default => $query,
        };
    }
}
