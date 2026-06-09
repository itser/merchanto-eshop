<?php

namespace Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Modules\Catalog\Services\ProductListingService;

class ProductController extends Controller
{
    public function index(ProductListingService $productListing): View
    {
        return view('catalog::products.index', [
            'products' => $productListing->listForStorefront(),
        ]);
    }
}
