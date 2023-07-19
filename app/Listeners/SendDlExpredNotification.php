<?php

namespace App\Listeners;

use App\Events\DlExpired;
use App\Models\User;
use App\Notifications\DlExpredNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendDlExpredNotification
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
     * @param  \App\Events\DlExpred  $event
     * @return void
     */
    public function handle( $event)
    {
        $driver = User::find($event->license->driver_id);
        $driver->notify(new DlExpredNotification($event->license, $driver));
    }
}
