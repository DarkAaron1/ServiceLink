<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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
        // Register Livewire components explicitly to ensure discovery
        if (class_exists(Livewire::class)) {
            Livewire::component('cocina-orders', \App\Http\Livewire\CocinaOrders::class);
        }
    }
}
