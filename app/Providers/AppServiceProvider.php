<?php

namespace App\Providers;

use App\Models\PengajuanRombongan;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.admin-dashboard', function ($view): void {
            $view->with(
                'pengajuanMenunggu',
                PengajuanRombongan::where('status', PengajuanRombongan::STATUS_DIAJUKAN)->count()
            );
        });
    }
}
