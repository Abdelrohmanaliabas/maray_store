<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'productsCount' => Product::count(),
            'newOrdersCount' => Order::where('status', 'new')->count(),
            'ordersCount' => Order::count(),
        ]);
    }
}

