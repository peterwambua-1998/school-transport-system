<?php

namespace App\Providers;

use App\Events\DlExpired;
use App\Events\ExpiredLicense;
use App\Events\InspectionDate;
use App\Events\InsuranceExpired;
use App\Listeners\SendDlExpredNotification;
use App\Listeners\SendExpiredLicenseNotification;
use App\Listeners\SendInpectionDateNotification;
use App\Listeners\SendInsuranceExpredNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        InsuranceExpired::class => [
            SendInsuranceExpredNotification::class
        ],
        InspectionDate::class => [
            SendInpectionDateNotification::class
        ],
        ExpiredLicense::class => [
            SendExpiredLicenseNotification::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
