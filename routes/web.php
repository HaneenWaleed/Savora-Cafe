<?php

use App\Models\Beverage;
use App\Models\FoodItem;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');
Route::get('/about', fn () => view('about'))->name('about');
Route::get('/contact', fn () => view('contact'))->name('contact');

Route::get('/login', fn () => view('auth.login'))->name('login');
Route::get('/register', fn () => view('auth.register'))->name('register');
Route::get('/admin', fn () => view('admin.dashboard'))->name('admin.dashboard');
Route::get('/admin/users', fn () => view('admin.management', ['page' => 'users']))->name('admin.users');
Route::get('/admin/food-items', fn () => view('admin.management', ['page' => 'food']))->name('admin.food-items');
Route::get('/admin/beverages', fn () => view('admin.management', ['page' => 'beverages']))->name('admin.beverages');
Route::get('/admin/categories', fn () => view('admin.management', ['page' => 'categories']))->name('admin.categories');
Route::get('/admin/orders', fn () => view('admin.management', ['page' => 'orders']))->name('admin.orders');
Route::get('/admin/customers', fn () => view('admin.management', ['page' => 'customers']))->name('admin.customers');
Route::get('/admin/statistics', fn () => view('admin.statistics'))->name('admin.statistics');
Route::get('/admin/ai', fn () => view('admin.ai'))->name('admin.ai');

// menu
Route::get('/menu', fn () => view('menu'))->name('menu');
Route::get('/menu/{type}/{id}', function (string $type, int $id) {
    abort_unless(in_array($type, ['food', 'beverage'], true), 404);

    $item = $type === 'food'
        ? FoodItem::with('category')->find($id)
        : Beverage::with('category')->find($id);

    abort_if(! $item, 404);

    return view('product', ['item' => $item, 'type' => $type]);
})->whereNumber('id')->name('product');

// Account pages
Route::get('/cart', fn () => view('cart'))->name('cart');
Route::get('/profile', fn () => view('profile'))->name('profile');
Route::get('/preferences', fn () => view('preferences'))->name('preferences');
Route::get('/orders', fn () => view('orders'))->name('orders');
Route::get('/favorites', fn () => view('favorites'))->name('favorites');
