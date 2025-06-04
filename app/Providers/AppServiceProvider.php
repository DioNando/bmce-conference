<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use App\View\Components\Card\Card;
use App\View\Components\Form\Form;
use App\View\Components\Table\Table;
use Illuminate\Support\Facades\Blade;
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
        // Register Blade components
        Blade::component('card', Card::class);
        Blade::component('form', Form::class);
        Blade::component('table', Table::class);

        // Register observers
        User::observe(UserObserver::class);

        // Disable Debugbar
        // \Debugbar::disable();
    }
}
