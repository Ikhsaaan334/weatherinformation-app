<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
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
        $frontend = rtrim(config('app.frontend_url'), '/');

        // Password reset links must point at the SPA, which then POSTs the token
        // back to POST /api/reset-password.
        ResetPassword::createUrlUsing(function ($notifiable, string $token) use ($frontend) {
            return $frontend.'/reset-password?token='.$token.'&email='.urlencode($notifiable->getEmailForPasswordReset());
        });

        // Email verification links must also land on the SPA. We generate the signed
        // API URL (GET /api/email/verify/{id}/{hash}) and hand it to the SPA, which
        // forwards it to the API verbatim to complete verification.
        VerifyEmail::createUrlUsing(function ($notifiable) use ($frontend) {
            $signedUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes((int) config('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );

            return $frontend.'/verify-email?verify_url='.urlencode($signedUrl);
        });
    }
}
