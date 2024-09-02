<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductDetailController;

Route::resource('products', ProductController::class);

// routes/web.php
Route::delete('/product-details/{id}', [ProductDetailController::class, 'destroy'])->name('product-details.destroy');