<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('dashboard', [
            'orders' => $user->orders()->latest()->limit(10)->get(),
            'purchasedItems' => OrderItem::query()
                ->whereHas('order', fn ($query) => $query
                    ->whereBelongsTo($user)
                    ->whereIn('status', ['paid', 'simulated']))
                ->latest()
                ->paginate(12),
        ]);
    }
}
