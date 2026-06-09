<?php

namespace Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Modules\Catalog\Models\Product;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('catalog::products.index', [
            'products' => $products,
        ]);
    }
}
