<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        // \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'is_disabled' => \App\Http\Middleware\Is_Disabled::class,
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'isAdmin' => \App\Http\Middleware\CheckIfAdmin::class,
        'settings' => \App\Http\Middleware\SettingsMiddleware::class,
        'isActiveTerm' => \App\Http\Middleware\IsTermActive::class,
        'is_office_staff' => \App\Http\Middleware\is_office_staff::class,
        'is_parent' => \App\Http\Middleware\is_parent::class,
        'redirect_home_admin' => \App\Http\Middleware\redirect_home_admin::class,
        'is_driver' => \App\Http\Middleware\is_driver::class,
        'is_attendant' => \App\Http\Middleware\is_attendant::class,
        'is_supervisor' => \App\Http\Middleware\is_supervisor::class,
        'is_teacher' => \App\Http\Middleware\is_teacher::class,
        'teacher_routes' => \App\Http\Middleware\teacher_routes::class,
        'is_head_teacher' => \App\Http\Middleware\is_head_teacher::class,
        'is_director' => \App\Http\Middleware\is_director::class,
        'email_settings' => \App\Http\Middleware\EmailSettings::class,
        'app_links' => \App\Http\Middleware\AppLinks::class,
        'payment_settings' => \App\Http\Middleware\PaymentSettings::class,
        'notification_settings' => \App\Http\Middleware\NotificationSettings::class,
        'whatsapp_settings' => \App\Http\Middleware\WhatsappSettings::class,
        'terminology' => \App\Http\Middleware\TerminologySettings::class,
        'staff' => \App\Http\Middleware\Staff::class,
        'license' => \App\Http\Middleware\license::class,
        'TermHoliday' => \App\Http\Middleware\TermHoliday::class,
        'TermSchoolFees' => \App\Http\Middleware\TermSchoolFees::class,
        'Grades' => \App\Http\Middleware\Grades::class,
        'Garage'=> \App\Http\Middleware\Garage::class,
        'vehicle' => \App\Http\Middleware\Vehicle::class,
        'Compliance' => \App\Http\Middleware\Compliance::class,
        'Maintenance' => \App\Http\Middleware\Maintenance::class,
        'parents' => \App\Http\Middleware\ParentMiddleware::class,
        'SmsSettings' => \App\Http\Middleware\Sms::class,
    ];
}
