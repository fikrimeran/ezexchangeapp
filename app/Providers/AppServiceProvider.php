<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Bridge\Sendgrid\Transport\SendgridApiTransport;

// Notification indicator
use App\Models\ExchangeRequest;
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
        // ✅ SendGrid mail driver (existing)
        Mail::extend('sendgrid', function (array $config) {
            return new SendgridApiTransport($config['api_key']);
        });

        // ✅ Notification indicator logic (NO database changes)
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $unreadNotifications = ExchangeRequest::where('to_user_id', Auth::id())
                    ->where('status', 'pending')
                    ->count();

                $view->with('unreadNotifications', $unreadNotifications);
            }
        });
    }
}
