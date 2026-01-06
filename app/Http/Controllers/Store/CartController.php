<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(CartService $cart)
    {
        return view('store.cart', [
            'cart' => $cart->hydrate(),
        ]);
    }

    public function add(Request $request, CartService $cart)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'qty' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::where('id', $data['product_id'])->where('is_active', true)->firstOrFail();
        $variant = ProductVariant::where('id', $data['variant_id'])->where('product_id', $product->id)->firstOrFail();

        if ($variant->quantity < (int) ($data['qty'] ?? 1)) {
            return back()->withErrors(['cart' => 'الكمية غير متاحة.']);
        }

        $cart->add($product->id, $variant->id, (int) ($data['qty'] ?? 1));

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'cart' => $cart->hydrate()]);
        }

        return redirect()->route('store.cart')->with('status', 'تمت الإضافة للسلة.');
    }

    public function update(Request $request, CartService $cart)
    {
        $data = $request->validate([
            'key' => ['required', 'string'],
            'qty' => ['required', 'integer', 'min:0'],
        ]);

        $cart->update($data['key'], (int) $data['qty']);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'cart' => $cart->hydrate()]);
        }

        return back()->with('status', 'تم تحديث السلة.');
    }

    public function remove(Request $request, CartService $cart)
    {
        $data = $request->validate([
            'key' => ['required', 'string'],
        ]);

        $cart->remove($data['key']);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'cart' => $cart->hydrate()]);
        }

        return back()->with('status', 'تم حذف العنصر.');
    }

    public function clear(Request $request, CartService $cart)
    {
        $cart->clear();

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('status', 'تم تفريغ السلة.');
    }
}

