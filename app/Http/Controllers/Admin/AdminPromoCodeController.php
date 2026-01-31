<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use App\Services\PromoCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminPromoCodeController extends Controller
{
    public function index(Request $request)
    {
        $query = PromoCode::query()->orderByDesc('id');

        if ($request->filled('q')) {
            $q = trim((string) $request->get('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('code', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%");
            });
        }

        return view('admin.promo_codes.index', [
            'promoCodes' => $query->paginate(20)->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('admin.promo_codes.create');
    }

    public function store(Request $request, PromoCodeService $promoService)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'name' => ['nullable', 'string', 'max:255'],
            'discount_type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:0.01'],
            'min_order_total' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['code'] = $promoService->normalize($data['code']);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['min_order_total'] = (float) ($data['min_order_total'] ?? 0);
        $data['max_discount'] = $data['max_discount'] !== null && $data['max_discount'] !== '' ? (float) $data['max_discount'] : null;
        $data['usage_limit'] = $data['usage_limit'] !== null && $data['usage_limit'] !== '' ? (int) $data['usage_limit'] : null;
        $data['starts_at'] = $data['starts_at'] !== null && $data['starts_at'] !== '' ? $data['starts_at'] : null;
        $data['ends_at'] = $data['ends_at'] !== null && $data['ends_at'] !== '' ? $data['ends_at'] : null;
        $data['created_by_admin_id'] = Auth::guard('admin')->id();

        if (PromoCode::query()->where('code', $data['code'])->exists()) {
            throw ValidationException::withMessages([
                'code' => 'الكود مستخدم بالفعل.',
            ]);
        }

        $promo = PromoCode::create($data);

        return redirect()->route('admin.promo-codes.edit', $promo)->with('status', 'تم إنشاء البروموكود.');
    }

    public function edit(PromoCode $promoCode)
    {
        return view('admin.promo_codes.edit', ['promoCode' => $promoCode]);
    }

    public function update(Request $request, PromoCode $promoCode, PromoCodeService $promoService)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'name' => ['nullable', 'string', 'max:255'],
            'discount_type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:0.01'],
            'min_order_total' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['code'] = $promoService->normalize($data['code']);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['min_order_total'] = (float) ($data['min_order_total'] ?? 0);
        $data['max_discount'] = $data['max_discount'] !== null && $data['max_discount'] !== '' ? (float) $data['max_discount'] : null;
        $data['usage_limit'] = $data['usage_limit'] !== null && $data['usage_limit'] !== '' ? (int) $data['usage_limit'] : null;
        $data['starts_at'] = $data['starts_at'] !== null && $data['starts_at'] !== '' ? $data['starts_at'] : null;
        $data['ends_at'] = $data['ends_at'] !== null && $data['ends_at'] !== '' ? $data['ends_at'] : null;

        if (
            PromoCode::query()
                ->where('code', $data['code'])
                ->where('id', '!=', $promoCode->id)
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'code' => 'الكود مستخدم بالفعل.',
            ]);
        }

        $promoCode->update($data);

        return back()->with('status', 'تم حفظ التعديلات.');
    }

    public function destroy(PromoCode $promoCode)
    {
        $promoCode->delete();

        return redirect()->route('admin.promo-codes.index')->with('status', 'تم حذف البروموكود.');
    }
}
