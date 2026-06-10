@extends('layouts.app')

@section('title', 'Products — ' . config('app.name'))

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Products</h1>
        <p class="mt-2 text-gray-600">Browse our catalog.</p>
    </div>

    @if ($products->isEmpty())
        <div class="rounded-lg border border-gray-200 bg-white p-8 text-center text-gray-600 shadow-sm">
            No products available
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($products as $product)
                <article class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900">
                        {{ $product->name }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        {{ $product->category->name }}
                    </p>

                    @if ($product->description)
                        <p class="mt-3 text-sm text-gray-600">
                            {{ $product->description }}
                        </p>
                    @endif

                    <dl class="mt-4 flex gap-6 text-sm">
                        <div>
                            <dt class="font-medium text-gray-500">Price</dt>
                            <dd class="text-gray-900">{{ $product->price }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Stock</dt>
                            <dd class="text-gray-900">{{ $product->stock }}</dd>
                        </div>
                    </dl>

                    <x-add-to-cart :product-id="$product->id" :can-add="$product->stock > 0" />
                </article>
            @endforeach
        </div>
    @endif
@endsection
