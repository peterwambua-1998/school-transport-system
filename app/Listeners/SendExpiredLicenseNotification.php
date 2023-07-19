<?php

namespace App\Listeners;

use App\Events\ExpiredLicense;
use App\Models\User;
use App\Notifications\DlExpredNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendExpiredLicenseNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ExpiredLicense  $event
     * @return void
     */
    public function handle(ExpiredLicense $event)
    {
        
       
    }
}
