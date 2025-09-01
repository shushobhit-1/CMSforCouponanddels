<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Coupon;
use App\Models\Deal;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'coupons' => Coupon::count(),
            'deals' => class_exists(Deal::class) ? Deal::count() : 0,
            'products' => class_exists(Product::class) ? Product::count() : 0,
        ];

        return view('admin.dashboard', compact('stats'));
    }
}

