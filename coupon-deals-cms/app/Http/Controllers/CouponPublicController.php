<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponPublicController extends Controller
{
    public function index()
    {
        $coupons = Coupon::where('is_active', true)->latest()->paginate(12);
        return view('public.coupons.index', compact('coupons'));
    }

    public function show(Coupon $coupon)
    {
        abort_unless($coupon->is_active, 404);
        return view('public.coupons.show', compact('coupon'));
    }
}

