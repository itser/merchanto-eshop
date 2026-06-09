@extends('layouts.app')

@section('title', config('app.name'))

@section('content')
    <div class="rounded-lg border border-gray-200 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-semibold text-gray-900">
            Welcome to {{ config('app.name') }}
        </h1>
        <p class="mt-3 text-gray-600">
            Browse the catalog and place orders through our storefront.
        </p>
    </div>
@endsection
