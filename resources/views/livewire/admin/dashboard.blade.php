<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-1 text-gray-600">A live snapshot of your bookstore.</p>
    </div>

    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg border-l-4 border-blue-500 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-gray-600">Order Revenue</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">₱{{ number_format((float) $totalRevenue, 2) }}</p>
            <p class="mt-2 text-sm text-gray-500">Excludes cancelled orders</p>
        </div>
        <div class="rounded-lg border-l-4 border-green-500 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-gray-600">Total Orders</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($totalOrders) }}</p>
            <p class="mt-2 text-sm text-gray-500">Across all order statuses</p>
        </div>
        <div class="rounded-lg border-l-4 border-purple-500 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-gray-600">Customers</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($totalCustomers) }}</p>
            <p class="mt-2 text-sm text-gray-500">Registered customer accounts</p>
        </div>
        <div class="rounded-lg border-l-4 border-orange-500 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-gray-600">Books in Catalog</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($totalBooks) }}</p>
            <p class="mt-2 text-sm text-gray-500">Active catalog records</p>
        </div>
    </div>

    <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <section class="rounded-lg bg-white p-6 shadow-sm lg:col-span-2">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-gray-900">Monthly Revenue</h2>
                <p class="text-sm text-gray-500">Order totals for the last six months, excluding cancellations.</p>
            </div>

            <div class="flex h-64 items-end justify-around gap-3 rounded-lg bg-gradient-to-b from-blue-50 to-gray-50 px-3 pt-4">
                @foreach ($monthlyRevenue as $month)
                    <div wire:key="revenue-{{ $month['label'] }}" class="flex h-full min-w-0 flex-1 flex-col items-center justify-end gap-2">
                        <span class="text-center text-xs text-gray-600">₱{{ number_format($month['amount'], 0) }}</span>
                        <div class="w-full max-w-12 rounded-t bg-blue-500" style="height: {{ $month['height'] }}%" title="₱{{ number_format($month['amount'], 2) }}"></div>
                        <span class="text-xs text-gray-600">{{ $month['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-lg bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-bold text-gray-900">Quick Actions</h2>
            <div class="space-y-3">
                <a href="{{ route('admin.books.create') }}" class="block rounded-lg bg-blue-600 px-4 py-3 text-center text-sm font-medium text-white transition hover:bg-blue-700">Add New Book</a>
                <a href="{{ route('admin.inventory.index') }}" class="block rounded-lg bg-orange-600 px-4 py-3 text-center text-sm font-medium text-white transition hover:bg-orange-700">Manage Inventory</a>
                <a href="{{ route('admin.purchase-orders.create') }}" class="block rounded-lg bg-green-600 px-4 py-3 text-center text-sm font-medium text-white transition hover:bg-green-700">Create Purchase Order</a>
                <a href="{{ route('admin.purchase-orders.index') }}" class="block rounded-lg bg-gray-700 px-4 py-3 text-center text-sm font-medium text-white transition hover:bg-gray-800">View Purchase Orders</a>
            </div>
        </section>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section class="rounded-lg bg-white p-6 shadow-sm">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Recent Orders</h2>
            </div>
            <div class="space-y-3">
                @forelse ($recentOrders as $order)
                    @php
                        $statusClasses = match ($order->status) {
                            'delivered' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                            'processing', 'out_for_delivery' => 'bg-blue-100 text-blue-800',
                            default => 'bg-yellow-100 text-yellow-800',
                        };
                    @endphp
                    <div wire:key="recent-order-{{ $order->id }}" class="flex items-center justify-between gap-4 rounded-lg bg-gray-50 p-4">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-gray-900">{{ $order->order_number }}</p>
                            <p class="mt-1 truncate text-xs text-gray-600">{{ $order->customer_name }} · {{ $order->created_at->format('M j, Y') }}</p>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="text-sm font-bold text-gray-900">₱{{ number_format((float) $order->total_amount, 2) }}</p>
                            <span class="mt-1 inline-block rounded px-2 py-1 text-xs font-medium {{ $statusClasses }}">{{ $order->status_label }}</span>
                        </div>
                    </div>
                @empty
                    <p class="rounded-lg bg-gray-50 p-6 text-center text-sm text-gray-500">No orders yet.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-lg bg-white p-6 shadow-sm">
            <h2 class="mb-6 text-lg font-bold text-gray-900">Top Selling Books</h2>
            <div class="space-y-3">
                @forelse ($topSellingBooks as $item)
                    <div wire:key="top-book-{{ $item->book_id ?? $loop->index }}" class="flex items-center justify-between gap-4 rounded-lg bg-gray-50 p-4">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-gray-900">{{ $item->book_title }}</p>
                            <p class="mt-1 truncate text-xs text-gray-600">{{ $item->book?->primaryAuthor?->name ?? 'Author unavailable' }}</p>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="text-sm font-bold text-gray-900">{{ number_format((int) $item->units_sold) }} sold</p>
                            <p class="mt-1 text-xs text-gray-600">₱{{ number_format((float) $item->revenue, 2) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="rounded-lg bg-gray-50 p-6 text-center text-sm text-gray-500">Sales data will appear here after the first order.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
