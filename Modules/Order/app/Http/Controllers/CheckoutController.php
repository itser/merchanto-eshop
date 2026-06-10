<?php

namespace Modules\Order\Http\Controllers;

use App\Exceptions\Catalog\InsufficientStockException;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Order\Http\Requests\CheckoutRequest;
use Modules\Order\Services\CheckoutService;

class CheckoutController extends Controller
{
    public function index(CheckoutService $checkout): View|RedirectResponse
    {
        if ($checkout->isEmpty()) {
            return redirect()->route('products.index');
        }

        return view('order::checkout.index', [
            'lines' => $checkout->lines(),
            'total' => $checkout->total(),
        ]);
    }

    public function store(CheckoutRequest $request, CheckoutService $checkout): RedirectResponse
    {
        if ($checkout->isEmpty()) {
            return redirect()->route('products.index');
        }

        try {
            $order = $checkout->place(
                customerName: $request->validated('customer_name'),
                customerEmail: $request->validated('customer_email'),
            );
        } catch (InsufficientStockException) {
            return redirect()
                ->route('checkout.index')
                ->withErrors(['checkout' => 'Some items in your cart are no longer available in the requested quantity.']);
        }

        return redirect()->route('orders.show', $order);
    }
}
