<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');
Route::get('/about', fn () => view('about'))->name('about');
Route::get('/contact', fn () => view('contact'))->name('contact');

Route::get('/login', fn () => view('auth.login'))->name('login');
Route::get('/register', fn () => view('auth.register'))->name('register');
Route::middleware('admin')->get('/admin', fn () => view('admin.dashboard'))->name('admin.dashboard');

// menu
Route::get('/menu', fn () => view('menu'))->name('menu');
Route::get('/menu/{type}/{id}', function (string $type, int $id) {
    abort_unless(in_array($type, ['food', 'beverage'], true), 404);

    $item = $type === 'food'
        ? \App\Models\FoodItem::with('category')->find($id)
        : \App\Models\Beverage::with('category')->find($id);

    abort_if(! $item, 404);

    return view('product', ['item' => $item, 'type' => $type]);
})->whereNumber('id')->name('product');

// Account pages
Route::get('/cart', fn () => view('cart'))->name('cart');
Route::get('/profile', fn () => view('profile'))->name('profile');
Route::get('/preferences', fn () => view('preferences'))->name('preferences');
Route::get('/orders', fn () => view('orders'))->name('orders');
Route::get('/favorites', fn () => view('favorites'))->name('favorites');
