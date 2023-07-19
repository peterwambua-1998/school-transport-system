<?php

namespace App\Listeners;

use App\Events\InspectionDate;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\InspectionDateNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendInpectionDateNotification
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
     * @param  \App\Events\InspectionDate  $event
     * @return void
     */
    public function handle(InspectionDate $event)
    {
        $users = User::where('user_type', 'LIKE', 'office staff')
        ->orWhere('user_type', 'LIKE', 'admin')
        ->orWhere('user_type', 'LIKE', 'supervisor')
        ->orWhere('user_type', 'LIKE', 'manager')
        ->orWhere('user_type', 'LIKE', 'office_executive')
        ->get();

        $vehicle = Vehicle::find($event->inspection->vehicle_id);

        $driver = User::find($vehicle->driver_id);
        $attendant = User::find($vehicle->attendant_id);
        
        
    }
}
