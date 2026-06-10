<a
    href="{{ url('/checkout') }}"
    class="relative inline-flex items-center gap-2 text-gray-600 hover:text-gray-900"
>
    Cart
    <span
        data-test="cart-count"
        @class([
            'inline-flex min-w-5 items-center justify-center rounded-full px-1.5 py-0.5 text-xs font-semibold',
            'bg-gray-900 text-white' => $count > 0,
            'bg-gray-200 text-gray-600' => $count === 0,
        ])
    >
        {{ $count }}
    </span>
</a>
