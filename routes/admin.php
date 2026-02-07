<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Admin\Login;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\SiteSettings;
use App\Livewire\Admin\Orders;
use App\Livewire\Admin\Products;
use App\Livewire\Admin\Customers;
use App\Livewire\Admin\ContactMessages;
use App\Livewire\Admin\Analytics;
use App\Livewire\Admin\Security;
use App\Livewire\Admin\AccessControl;
use App\Livewire\Admin\Users;
use App\Livewire\Admin\AuditLogs;
use App\Services\AuditService;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', Login::class)->middleware('guest')->name('login');

    Route::post('/logout', function () {
        AuditService::logout();
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('admin.login');
    })->middleware('auth')->name('logout');

    Route::get('/', Dashboard::class)
        ->middleware(['auth', 'role:super-admin|admin'])
        ->name('dashboard');

    Route::get('/orders', Orders::class)
        ->middleware(['auth', 'role:super-admin|admin'])
        ->name('orders');

    Route::get('/products', Products::class)
        ->middleware(['auth', 'role:super-admin|admin'])
        ->name('products');

    Route::get('/customers', Customers::class)
        ->middleware(['auth', 'role:super-admin|admin'])
        ->name('customers');

    Route::get('/messages', ContactMessages::class)
        ->middleware(['auth', 'role:super-admin|admin'])
        ->name('messages');

    Route::get('/analytics', Analytics::class)
        ->middleware(['auth', 'role:super-admin|admin'])
        ->name('analytics');

    Route::get('/security', Security::class)
        ->middleware(['auth', 'role:super-admin|admin'])
        ->name('security');

    Route::get('/access', AccessControl::class)
        ->middleware(['auth', 'role:super-admin|admin'])
        ->name('access');

    Route::get('/users', Users::class)
        ->middleware(['auth', 'role:super-admin|admin'])
        ->name('users');

    Route::get('/audit', AuditLogs::class)
        ->middleware(['auth', 'role:super-admin|admin'])
        ->name('audit');

    Route::get('/settings', SiteSettings::class)
        ->middleware(['auth', 'role:super-admin|admin'])
        ->name('settings');
});
