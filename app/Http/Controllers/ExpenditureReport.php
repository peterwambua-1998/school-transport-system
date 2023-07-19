<?php

namespace App\Http\Controllers;

use App\Models\BusMaintenance;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class ExpenditureReport extends Controller
{
    /**
     * routine maintenance report
     */
    public function routineMaintenance()
    {
        $vehicles = Vehicle::all();
        foreach ($vehicles as $key => $vehicle) {
            //get routine maintenance for each
            $routine = BusMaintenance::where('vehicle_id','=', $vehicle->id)->where('status','=','routine')->get();
            $vehicle->routine = $routine;
        }

        return response($vehicle);
    }


    /**
     * off routine maintenance report
     */
    public function offRoutineMaintenance()
    {
        $vehicles = Vehicle::all();
        foreach ($vehicles as $key => $vehicle) {
            //get routine maintenance for each
            $off_routine = BusMaintenance::where('vehicle_id','=', $vehicle->id)->where('status','=','off routine')->get();
            $vehicle->off_routine = $off_routine;
        }

        return response($vehicle);
    }
}
