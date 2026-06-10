<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Modules\Order\Models\Order;
use Modules\Order\Services\OrderViewService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderController extends Controller
{
    public function show(Order $order, OrderViewService $orderView): View
    {
        $orderWithItems = $orderView->find($order->id);

        if ($orderWithItems === null) {
            throw new NotFoundHttpException;
        }

        return view('order::orders.show', [
            'order' => $orderWithItems,
        ]);
    }
}
