<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

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
        View::composer('*', function ($view) {
            $user = Auth::user();
            $adminName = null;
            $adminAvatar = null;

            if ($user && $user->role === 'admin') {
                $adminName = $user->name;
                $adminAvatar = $user->foto;
            }

            $view->with([
                'adminName' => $adminName,
                'adminAvatar' => $adminAvatar,
            ]);
        });
    }
}
