<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Eloquent\User;
use Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot ()
    {
        view()->composer('layout.header', function($view) {
            if (Auth::check()) {
                $view->with('roles', User::getRoles(Auth::id()));
            } else {
                $view->with('roles', null);
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
