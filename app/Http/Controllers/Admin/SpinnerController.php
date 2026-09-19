<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpinnerPrize;
use App\Models\SpinnerSpin;
use Illuminate\View\View;

class SpinnerController extends Controller
{
    /**
     * List every spin: which device/number played and what they won.
     */
    public function index(): View
    {
        $query = SpinnerSpin::with(['prize', 'coupon']);

        // Search by phone number or coupon code.
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('phone_number', 'like', "%{$search}%")
                    ->orWhere('device_id', 'like', "%{$search}%")
                    ->orWhereHas('coupon', fn ($c) => $c->where('code', 'like', "%{$search}%"));
            });
        }

        // Filter by result: win / try_again / no_prize.
        if (request()->filled('result')) {
            if (request('result') === 'win') {
                $query->where('is_win', true);
            } elseif (request('result') === 'redeemed') {
                $query->where('is_redeemed', true);
            } else {
                $query->where('result_type', request('result'));
            }
        }

        // Date filtering reuses the same options used elsewhere in the admin.
        if (request()->filled('date')) {
            match (request('date')) {
                'today' => $query->whereDate('played_on', today()),
                'week' => $query->whereBetween('played_on', [now()->startOfWeek(), now()->endOfWeek()]),
                'month' => $query->whereMonth('played_on', now()->month)->whereYear('played_on', now()->year),
                default => null,
            };
        }

        $spins = $query->latest('id')->paginate(25)->withQueryString();

        // Summary counters shown at the top of the page.
        $stats = [
            'total' => SpinnerSpin::count(),
            'wins' => SpinnerSpin::where('is_win', true)->count(),
            'redeemed' => SpinnerSpin::where('is_redeemed', true)->count(),
            'today' => SpinnerSpin::whereDate('played_on', today())->count(),
        ];

        $prizes = SpinnerPrize::orderBy('sort_order')->get();

        return view('admin.spinner.index', compact('spins', 'stats', 'prizes'));
    }
}
