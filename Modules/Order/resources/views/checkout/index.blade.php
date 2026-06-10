@extends('layouts.app')

@section('title', 'Checkout — ' . config('app.name'))

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Checkout</h1>
        <p class="mt-2 text-gray-600">Review your cart and place your order.</p>
    </div>

    <div class="grid gap-8 lg:grid-cols-2">
        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Your items</h2>

            <ul class="mt-4 divide-y divide-gray-200">
                @foreach ($lines as $line)
                    <li class="flex items-start justify-between gap-4 py-4 first:pt-0 last:pb-0">
                        <div>
                            <p class="font-medium text-gray-900">{{ $line->productName }}</p>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $line->productPrice }} × {{ $line->quantity }}
                            </p>
                        </div>
                        <p class="text-sm font-medium text-gray-900">{{ $line->lineTotal }}</p>
                    </li>
                @endforeach
            </ul>

            <dl class="mt-6 flex items-center justify-between border-t border-gray-200 pt-4">
                <dt class="text-sm font-medium text-gray-500">Total</dt>
                <dd class="text-lg font-semibold text-gray-900">{{ $total }}</dd>
            </dl>
        </section>

        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Customer details</h2>

            <form method="POST" action="{{ route('checkout.store') }}" class="mt-4 space-y-4">
                @csrf

                <div>
                    <label for="customer_name" class="block text-sm font-medium text-gray-700">
                        Name
                    </label>
                    <input
                        id="customer_name"
                        name="customer_name"
                        type="text"
                        value="{{ old('customer_name') }}"
                        required
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                    >
                    @error('customer_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="customer_email" class="block text-sm font-medium text-gray-700">
                        Email
                    </label>
                    <input
                        id="customer_email"
                        name="customer_email"
                        type="email"
                        value="{{ old('customer_email') }}"
                        required
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900"
                    >
                    @error('customer_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @error('checkout')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror

                <button
                    type="submit"
                    class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
                >
                    Place order
                </button>
            </form>
        </section>
    </div>
@endsection
