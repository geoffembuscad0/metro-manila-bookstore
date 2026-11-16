<?php

namespace App\Livewire\Admin;

use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $revenueOrders = Order::query()->where('status', '!=', 'cancelled');

        $totalRevenue = (clone $revenueOrders)->sum('total_amount');
        $totalOrders = Order::query()->count();
        $totalCustomers = User::query()->role('User')->count();
        $totalBooks = Book::query()->count();

        $chartStart = now()->startOfMonth()->subMonths(5);
        $revenueByMonth = (clone $revenueOrders)
            ->where('placed_at', '>=', $chartStart)
            ->get(['placed_at', 'total_amount'])
            ->groupBy(fn (Order $order) => $order->placed_at?->format('Y-m'))
            ->map(fn ($orders) => (float) $orders->sum('total_amount'));

        $monthlyRevenue = collect(range(5, 0))->map(function (int $monthsAgo) use ($revenueByMonth) {
            $month = now()->startOfMonth()->subMonths($monthsAgo);

            return [
                'label' => $month->format('M'),
                'amount' => $revenueByMonth->get($month->format('Y-m'), 0),
            ];
        });

        $maxMonthlyRevenue = (float) $monthlyRevenue->max('amount');
        $monthlyRevenue = $monthlyRevenue->map(function (array $month) use ($maxMonthlyRevenue) {
            $month['height'] = $maxMonthlyRevenue > 0
                ? max(8, (int) round(($month['amount'] / $maxMonthlyRevenue) * 100))
                : 8;

            return $month;
        });

        $recentOrders = Order::query()
            ->latest('created_at')
            ->limit(5)
            ->get(['id', 'order_number', 'customer_name', 'status', 'total_amount', 'created_at']);

        $topSellingBooks = OrderItem::query()
            ->whereHas('order', fn ($query) => $query->where('status', '!=', 'cancelled'))
            ->selectRaw('book_id, book_title, SUM(quantity) as units_sold, SUM(line_total) as revenue')
            ->with('book.primaryAuthor')
            ->groupBy('book_id', 'book_title')
            ->orderByDesc('units_sold')
            ->limit(5)
            ->get();

        return view('livewire.admin.dashboard', [
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'totalCustomers' => $totalCustomers,
            'totalBooks' => $totalBooks,
            'monthlyRevenue' => $monthlyRevenue,
            'recentOrders' => $recentOrders,
            'topSellingBooks' => $topSellingBooks,
        ])->layout('layouts.admin');
    }
}
