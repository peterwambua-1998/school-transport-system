<?php

namespace App\Listeners;

use App\Events\WarrantyExpired;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWarrantyExpiredNotification
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
     * @param  \App\Events\WarrantyExpired  $event
     * @return void
     */
    public function handle(WarrantyExpired $event)
    {
        //
    }
}
