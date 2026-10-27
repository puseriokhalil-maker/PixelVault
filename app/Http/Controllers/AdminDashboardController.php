<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $simulatedOrders = Order::query()->where('status', 'simulated');

        return view('admin.dashboard', [
            'simulatedSalesTotal' => (int) (clone $simulatedOrders)->sum('total_amount'),
            'simulatedOrderCount' => (clone $simulatedOrders)->count(),
            'orders' => $simulatedOrders
                ->with(['artist', 'items', 'user'])
                ->latest('id')
                ->paginate(20),
        ]);
    }
}
