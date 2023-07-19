<?php

namespace App\Listeners;

use App\Events\InsuranceExpired;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\InsuraceExpiredNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendInsuranceExpredNotification
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
     * @param  \App\Events\InsuranceExpired  $event
     * @return void
     */
    public function handle(InsuranceExpired $event)
    {
        dd($event);
        
        $users = User::where('user_type', 'LIKE', 'office staff')
        ->orWhere('user_type', 'LIKE', 'admin')
        ->orWhere('user_type', 'LIKE', 'supervisor')
        ->orWhere('user_type', 'LIKE', 'manager')
        ->orWhere('user_type', 'LIKE', 'office_executive')
        ->get();

        $vehicle = Vehicle::find($event->insurance->vehicle_id);

        Notification::send($users, new InsuraceExpiredNotification($event->insurance, $vehicle));
    }
}
