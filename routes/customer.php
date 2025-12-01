<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Customer\HomeController;
use App\Http\Controllers\Api\Customer\CartController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

# Home
Route::prefix('home')->name('home.')->group(function(){
    Route::get('index', [HomeController::class, 'index'])->name('index');
});

# Cart
Route::prefix('cart')->name('cart.')->group(function(){
    Route::post('save', [CartController::class, 'save'])->name('save');
    Route::post('remove', [CartController::class,'remove'])->name('remove');
});
