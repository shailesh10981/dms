<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Paginator::useBootstrap();
        // ✅ Only force HTTPS in production
        if (App::environment('production')) {
            URL::forceScheme('https');
        }

        \Illuminate\Pagination\Paginator::useBootstrap();

        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $view->with('currentRoute', \Illuminate\Support\Facades\Route::currentRouteName());
        });
    }
}
