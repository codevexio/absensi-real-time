<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Karyawan;
use App\Observers\KaryawanObserver;

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
        // Daftarkan observer Karyawan
        Karyawan::observe(KaryawanObserver::class);
    }
}
