<div>
    @if ($canAdd)
        <button
            type="button"
            wire:click="addToCart"
            class="mt-4 rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
        >
            Add to cart
        </button>
    @else
        <p class="mt-4 text-sm font-medium text-gray-500">
            Out of stock
        </p>
    @endif
</div>
