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
        Livewire::component('vendor.all-invoice', \App\Livewire\Vendor\AllInvoice::class);
        Livewire::component('vendor.submit',      \App\Livewire\Vendor\Submit::class);
        \Carbon\Carbon::setLocale('en');
        config(['app.timezone' => 'Asia/Kuala_Lumpur']);
    }
}
