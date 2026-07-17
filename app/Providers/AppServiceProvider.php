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
            // Retrieve actual settings from DB, falling back to empty array if not migrated
            try {
                $dbSettings = \App\Models\Setting::pluck('value', 'key')->toArray();
            } catch (\Exception $e) {
                $dbSettings = [];
            }
            $view->with('siteSettings', $dbSettings);
        });

        // Set Google Auth config dynamically
        try {
            $dbSettings = \App\Models\Setting::pluck('value', 'key')->toArray();
            if (!empty($dbSettings['google_login_active']) && $dbSettings['google_login_active'] == '1') {
                config([
                    'services.google.client_id' => $dbSettings['google_client_id'] ?? '',
                    'services.google.client_secret' => $dbSettings['google_client_secret'] ?? '',
                    'services.google.redirect' => url('/auth/google/callback'),
                ]);
            }
        } catch (\Exception $e) {
            // DB not ready yet
        }
    }
}
