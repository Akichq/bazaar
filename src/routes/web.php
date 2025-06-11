<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// トップページ
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 認証関連のルート
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])
    ->name('register');

Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->name('login');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

// 商品表示関連のルート
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');
Route::post('/item/{item}/comment', [ItemController::class, 'comment'])->name('items.comment');

// 認証が必要なルート
Route::middleware(['auth'])->group(function () {
    // マイページ関連
    Route::get('/mypage', [UserController::class, 'index'])->name('users.mypage');
    Route::get('/mypage/profile', [UserController::class, 'editProfile'])->name('users.editProfile');
    Route::post('/mypage/profile', [UserController::class, 'updateProfile'])->name('users.profile.update');

    // 商品出品関連
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');

    // 購入関連
    Route::get('/purchase/{item}', [ItemController::class, 'purchase'])->name('items.purchase');
    Route::post('/purchase/{item}', [ItemController::class, 'purchaseStore'])->name('items.purchase.store');
    Route::get('/purchase/{item}/success', [ItemController::class, 'purchaseSuccess'])->name('items.purchase.success');
    Route::get('/purchase/address/{item}', [AddressController::class, 'edit'])->name('address.edit');
    Route::post('/purchase/address/{item}', [AddressController::class, 'update'])->name('address.update');

    // お気に入り関連
    Route::post('/item/{item}/favorite', [ItemController::class, 'favorite'])->name('items.favorite');
});

// リダイレクトルート
Route::get('/home', function () {
    return redirect()->route('items.index');
})->name('home');

Route::get('/dashboard', function () {
    return redirect()->route('items.index');
})->name('dashboard');