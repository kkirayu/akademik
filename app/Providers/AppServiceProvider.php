<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Mahasiswa;          
use App\Observers\GeneralObserver;  

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
        // Sekarang Laravel sudah tau "Mahasiswa" itu yang dari folder Models
        Mahasiswa::observe(GeneralObserver::class);
    }
}