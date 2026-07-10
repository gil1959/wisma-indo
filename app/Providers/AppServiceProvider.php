<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                return true;
            }
            return null;
        });

        // Basic settings shared to all views
        View::composer('*', function ($view) {
            $view->with('siteSettings', [
                'site_logo' => asset('images/logo.png'),
                'seo_site_title' => 'Rumaindo - Portal Properti Terpercaya',
            ]);
        });
    }
}
