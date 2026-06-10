<?php

use Illuminate\Support\Facades\Route;
use Modules\Catalog\Http\Controllers\ProductController;

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
