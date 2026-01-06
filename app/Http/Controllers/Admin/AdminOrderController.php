<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->get('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('order_number', 'like', "%{$q}%")
                    ->orWhere('customer_name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        return view('admin.orders.index', [
            'orders' => $query->paginate(20)->withQueryString(),
        ]);
    }

    public function show(Order $order)
    {
        $order->load('items');

        return view('admin.orders.show', ['order' => $order]);
    }

    public function create()
    {
        $variants = ProductVariant::query()
            ->with('product')
            ->orderBy('product_id')
            ->orderBy('color')
            ->orderBy('size')
            ->get();

        return view('admin.orders.create', [
            'variants' => $variants,
            'statuses' => ['new', 'confirmed', 'shipped', 'delivered', 'canceled'],
        ]);
    }

    public function store(Request $request, PricingService $pricing)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'phone2' => ['nullable', 'string', 'max:30'],
            'governorate' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['nullable', 'in:new,confirmed,shipped,delivered,canceled'],
            'items.variant_id' => ['required', 'array', 'min:1'],
            'items.variant_id.*' => ['required', 'integer', 'exists:product_variants,id'],
            'items.qty' => ['required', 'array', 'min:1'],
            'items.qty.*' => ['required', 'integer', 'min:1'],
        ]);

        $variantIds = array_map('intval', $data['items']['variant_id']);
        $qtys = array_map('intval', $data['items']['qty']);

        return DB::transaction(function () use ($data, $variantIds, $qtys, $pricing) {
            $variants = ProductVariant::query()
                ->with(['product.bulkDiscounts'])
                ->whereIn('id', $variantIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $items = [];
            $subtotal = 0.0;
            $discountTotal = 0.0;

            foreach ($variantIds as $idx => $variantId) {
                $variant = $variants->get($variantId);
                $qty = (int) ($qtys[$idx] ?? 0);
                if (! $variant || $qty < 1) {
                    continue;
                }

                if ($variant->quantity < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "الكمية غير متاحة للمنتج: {$variant->product->name} ({$variant->color}/{$variant->size}).",
                    ]);
                }

                $unitPrice = (float) $variant->product->price;
                $lineSubtotal = $unitPrice * $qty;
                $lineDiscount = $pricing->discountForProductQty($variant->product, $qty, $unitPrice);
                $lineTotal = max(0.0, $lineSubtotal - $lineDiscount);

                $subtotal += $lineSubtotal;
                $discountTotal += $lineDiscount;

                $items[] = [
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'color' => $variant->color,
                    'size' => $variant->size,
                    'unit_price' => $unitPrice,
                    'qty' => $qty,
                    'line_subtotal' => $lineSubtotal,
                    'line_discount' => $lineDiscount,
                    'line_total' => $lineTotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $variant->decrement('quantity', $qty);
            }

            if (! $items) {
                throw ValidationException::withMessages([
                    'items' => 'لازم تضيف عناصر للأوردر.',
                ]);
            }

            $order = Order::create([
                'order_number' => 'MS-'.strtoupper(Str::random(8)),
                'status' => $data['status'] ?? 'new',
                'customer_name' => $data['customer_name'],
                'phone' => $data['phone'],
                'phone2' => $data['phone2'] ?? null,
                'governorate' => $data['governorate'],
                'address' => $data['address'],
                'notes' => $data['notes'] ?? null,
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'total' => max(0.0, $subtotal - $discountTotal),
                'payment_method' => 'cod',
                'created_by_admin_id' => Auth::guard('admin')->id(),
            ]);

            foreach ($items as &$item) {
                $item['order_id'] = $order->id;
            }
            unset($item);

            OrderItem::insert($items);

            return redirect()->route('admin.orders.show', $order)->with('status', 'تم إنشاء الأوردر.');
        });
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,confirmed,shipped,delivered,canceled'],
        ]);

        $order->update(['status' => $data['status']]);

        return back()->with('status', 'تم تحديث الحالة.');
    }
}
