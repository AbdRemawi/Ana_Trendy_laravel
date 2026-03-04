<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCouponRequest;
use App\Http\Requests\Admin\UpdateCouponRequest;
use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(): View
    {
        $query = Coupon::withCount('orders');

        if (request()->filled('search')) {
            $search = request('search');
            $query->where('code', 'like', "%{$search}%");
        }

        if (request()->filled('type')) {
            $query->where('type', request('type'));
        }

        if (request()->filled('status')) {
            if (request('status') === 'active') {
                $query->where('is_active', true);
            } elseif (request('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $coupons = $query->latest()->paginate(20);

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create(): View
    {
        return view('admin.coupons.create');
    }

    public function store(StoreCouponRequest $request): RedirectResponse
    {
        $coupon = Coupon::create($request->validated());

        return redirect()
            ->route('admin.coupons.show', $coupon)
            ->with('success', __('admin.coupon_created_successfully'));
    }

    public function show(Coupon $coupon): View
    {
        $coupon->load('orders');

        $totalDiscountGiven = Order::where('coupon_id', $coupon->id)
            ->whereNotNull('coupon_id')
            ->sum('coupon_discount_amount');

        return view('admin.coupons.show', compact('coupon', 'totalDiscountGiven'));
    }

    public function edit(Coupon $coupon): View
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update($request->validated());

        return redirect()
            ->route('admin.coupons.show', $coupon)
            ->with('success', __('admin.coupon_updated_successfully'));
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $code = $coupon->code;
        $orderCount = $coupon->orders()->count();

        if ($orderCount > 0) {
            return back()
                ->with('error', __('admin.cannot_delete_used_coupon', ['count' => $orderCount]));
        }

        $coupon->delete();

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', __('admin.coupon_deleted_successfully', ['code' => $code]));
    }

    public function toggleStatus(Coupon $coupon): RedirectResponse
    {
        $coupon->update(['is_active' => !$coupon->is_active]);

        $message = $coupon->is_active
            ? __('admin.coupon_activated')
            : __('admin.coupon_deactivated');

        return back()->with('success', $message);
    }
}
