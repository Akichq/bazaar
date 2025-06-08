<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

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

Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');
Route::post('/item/{item}/comment', [ItemController::class, 'comment'])->name('items.comment');
Route::get('/purchase/{item}', [ItemController::class, 'purchase'])->name('items.purchase');
Route::post('/purchase/{item}', [ItemController::class, 'purchaseStore'])->name('items.purchase.store');
Route::get('/purchase/{item}/success', [ItemController::class, 'purchaseSuccess'])->name('items.purchase.success');
Route::get('/purchase/address/{item}', [AddressController::class, 'edit'])->name('address.edit');
Route::post('/purchase/address/{item}', [AddressController::class, 'update'])->name('address.update');
Route::get('/mypage', [UserController::class, 'index'])->middleware('auth')->name('users.mypage');
Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
Route::post('/sell', [ItemController::class, 'store'])->name('items.store');
Route::post('/item/{item}/favorite', [ItemController::class, 'favorite'])->name('items.favorite');
Route::get('/home', function () {
    return redirect()->route('items.index');
});
Route::get('/dashboard', function () {
    return redirect()->route('items.index');
});
Route::get('/mypage/profile', [UserController::class, 'editProfile'])->name('users.profile.edit');
Route::post('/mypage/profile', [UserController::class, 'updateProfile'])->name('users.profile.update');
Route::get('/mypage/profile/edit', [UserController::class, 'editProfile'])->name('users.editProfile');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');