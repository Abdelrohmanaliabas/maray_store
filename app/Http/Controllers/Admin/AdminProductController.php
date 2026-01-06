<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BulkDiscount;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with('category')->orderByDesc('id');

        if ($request->filled('q')) {
            $q = trim((string) $request->get('q'));
            $query->where('name', 'like', "%{$q}%");
        }

        return view('admin.products.index', [
            'products' => $query->paginate(20)->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('admin.products.create', [
            'categories' => Category::orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'images.*' => ['nullable', 'image', 'max:5120'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['name']);

        return DB::transaction(function () use ($request, $data) {
            $product = Product::create($data);

            $this->ensureHasVariants($request);
            $this->syncVariants($request, $product);
            $this->syncDiscounts($request, $product);
            $this->storeImages($request, $product);

            return redirect()->route('admin.products.edit', $product)->with('status', 'تم إنشاء المنتج.');
        });
    }

    public function edit(Product $product)
    {
        $product->load(['images', 'variants', 'bulkDiscounts']);

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'images.*' => ['nullable', 'image', 'max:5120'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['name'], $product->id);

        return DB::transaction(function () use ($request, $product, $data) {
            $product->update($data);

            $this->ensureHasVariants($request);
            $this->syncVariants($request, $product);
            $this->syncDiscounts($request, $product);
            $this->storeImages($request, $product);

            return back()->with('status', 'تم حفظ التعديلات.');
        });
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'تم حذف المنتج.');
    }

    public function destroyImage(Product $product, ProductImage $image)
    {
        abort_unless($image->product_id === $product->id, 404);

        $path = $image->path;
        $image->delete();

        if (str_starts_with($path, 'uploads/')) {
            $fullPath = public_path($path);
            if (is_file($fullPath)) {
                @unlink($fullPath);
            }
        }

        return back()->with('status', 'تم حذف الصورة.');
    }

    private function uniqueSlug(?string $slug, string $name, ?int $ignoreId = null): string
    {
        $raw = trim((string) $slug);
        $base = $raw !== '' ? Str::slug($raw) : Str::slug($name);
        $base = $base !== '' ? $base : 'product';

        $candidate = $base;
        $i = 2;

        while (
            Product::query()
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $candidate)
                ->exists()
        ) {
            $candidate = "{$base}-{$i}";
            $i++;
        }

        return $candidate;
    }

    private function ensureHasVariants(Request $request): void
    {
        $colors = (array) $request->input('variants.color', []);
        $sizes = (array) $request->input('variants.size', []);

        $count = max(count($colors), count($sizes));
        for ($i = 0; $i < $count; $i++) {
            $color = trim((string) ($colors[$i] ?? ''));
            $size = trim((string) ($sizes[$i] ?? ''));
            if ($color !== '' && $size !== '') {
                return;
            }
        }

        throw ValidationException::withMessages([
            'variants' => 'لازم تضيف لون ومقاس واحد على الأقل.',
        ]);
    }

    private function syncVariants(Request $request, Product $product): void
    {
        $colors = (array) $request->input('variants.color', []);
        $sizes = (array) $request->input('variants.size', []);
        $qtys = (array) $request->input('variants.quantity', []);

        $rows = [];
        $count = max(count($colors), count($sizes), count($qtys));
        for ($i = 0; $i < $count; $i++) {
            $color = trim((string) ($colors[$i] ?? ''));
            $size = trim((string) ($sizes[$i] ?? ''));
            $qty = (int) ($qtys[$i] ?? 0);

            if ($color === '' || $size === '') {
                continue;
            }

            $rows[] = [
                'product_id' => $product->id,
                'color' => $color,
                'size' => $size,
                'quantity' => max(0, $qty),
            ];
        }

        ProductVariant::where('product_id', $product->id)->delete();
        if ($rows) {
            ProductVariant::insert($rows);
        }
    }

    private function syncDiscounts(Request $request, Product $product): void
    {
        $mins = (array) $request->input('discounts.min_qty', []);
        $types = (array) $request->input('discounts.discount_type', []);
        $values = (array) $request->input('discounts.value', []);
        $actives = (array) $request->input('discounts.is_active', []);

        $rows = [];
        $count = max(count($mins), count($types), count($values));
        for ($i = 0; $i < $count; $i++) {
            $minQty = (int) ($mins[$i] ?? 0);
            $type = (string) ($types[$i] ?? '');
            $value = (float) ($values[$i] ?? 0);
            $active = isset($actives[$i]) ? (bool) $actives[$i] : true;

            if ($minQty <= 1 || ! in_array($type, ['percent', 'fixed'], true) || $value <= 0) {
                continue;
            }

            $rows[] = [
                'product_id' => $product->id,
                'min_qty' => $minQty,
                'discount_type' => $type,
                'value' => $value,
                'is_active' => $active,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        BulkDiscount::where('product_id', $product->id)->delete();
        if ($rows) {
            BulkDiscount::insert($rows);
        }
    }

    private function storeImages(Request $request, Product $product): void
    {
        $files = $request->file('images', []);
        if (! is_array($files) || count($files) === 0) {
            return;
        }

        $nextSort = (int) ($product->images()->max('sort_order') ?? 0) + 1;
        foreach ($files as $file) {
            if (! $file) {
                continue;
            }

            $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
            $dir = public_path('uploads/products');
            if (! is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }

            $file->move($dir, $filename);

            $product->images()->create([
                'path' => 'uploads/products/'.$filename,
                'sort_order' => $nextSort,
            ]);
            $nextSort++;
        }
    }
}
