<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ItemController::class, 'index']);
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');
Route::post('/item/{item}/comment', [ItemController::class, 'comment'])->name('items.comment');
Route::get('/item/{item}/purchase', [ItemController::class, 'purchase'])->name('items.purchase');
Route::post('/item/{item}/purchase', [ItemController::class, 'purchaseStore'])->name('items.purchase.store');
Route::get('/address/edit', [AddressController::class, 'edit'])->name('address.edit');
Route::post('/address/update', [AddressController::class, 'update'])->name('address.update');
Route::get('/mypage', [UserController::class, 'index'])->name('users.mypage');