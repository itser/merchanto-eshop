<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Order\Models\Order;

class CheckoutController extends Controller
{
    public function index(): never
    {
        abort(501);
    }

    public function store(Request $request): never
    {
        abort(501);
    }
}
