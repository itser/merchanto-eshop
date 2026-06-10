@extends('layouts.app')

@section('title', 'Order #'.$order->id.' — '.config('app.name'))

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Order #{{ $order->id }}</h1>
        <p class="mt-2 text-gray-600">Thank you for your order.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Customer</h2>

            <dl class="mt-4 space-y-3 text-sm">
                <div>
                    <dt class="font-medium text-gray-500">Name</dt>
                    <dd class="text-gray-900">{{ $order->customer_name }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500">Email</dt>
                    <dd class="text-gray-900">{{ $order->customer_email }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500">Status</dt>
                    <dd class="text-gray-900">{{ ucfirst($order->status->value) }}</dd>
                </div>
            </dl>
        </section>

        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Items</h2>

            <ul class="mt-4 divide-y divide-gray-200">
                @foreach ($order->items as $item)
                    <li class="flex items-start justify-between gap-4 py-4 first:pt-0 last:pb-0">
                        <div>
                            <p class="font-medium text-gray-900">{{ $item->product_name }}</p>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $item->product_price }} × {{ $item->quantity }}
                            </p>
                        </div>
                        <p class="text-sm font-medium text-gray-900">
                            {{ bcmul($item->product_price, (string) $item->quantity, 2) }}
                        </p>
                    </li>
                @endforeach
            </ul>

            <dl class="mt-6 flex items-center justify-between border-t border-gray-200 pt-4">
                <dt class="text-sm font-medium text-gray-500">Total</dt>
                <dd class="text-lg font-semibold text-gray-900">{{ $order->total }}</dd>
            </dl>
        </section>
    </div>
@endsection
