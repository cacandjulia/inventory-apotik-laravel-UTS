<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Gate 'admin' dipakai di route (middleware can:admin) dan di Blade (@can('admin'))
        Gate::define('admin', fn (User $user) => $user->role === 'admin');

        // Admin & Staff boleh kelola stok (pembelian, stok rendah); Pembeli hanya belanja
        Gate::define('manage-stock', fn (User $user) => in_array($user->role, ['admin', 'staff']));

        Paginator::defaultView('pagination.lilac');
    }
}
