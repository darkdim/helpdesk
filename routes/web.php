<?php

use App\Http\Controllers\HelloController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HelloController::class, 'index']);

Route::resource('products', ProductController::class);
