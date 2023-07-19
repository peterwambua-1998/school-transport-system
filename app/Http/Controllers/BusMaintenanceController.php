<?php

namespace App\Http\Controllers;

use App\Models\BusMaintenance;
use App\Models\BusMaintenanceImage;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class BusMaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vehicles = Vehicle::where('status','=',1)->get();

        return view('maintenance.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BusMaintenance  $busMaintenance
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vehicle = Vehicle::find(Crypt::decrypt($id));

        $dailys = BusMaintenance::where('status','=', 'daily')->where('vehicle_id','=',$vehicle->id)->orderBy('created_at','desc')->get();
        foreach ($dailys as $key => $daily) {
            $image = BusMaintenanceImage::where('bus_maintenance_id','=', $daily->id)->get();
            $daily->images = $image;
        }


        $routines = BusMaintenance::where('status','=', 'routine')->where('vehicle_id','=',$vehicle->id)->orderBy('created_at','desc')->get();
        foreach ($routines as $key => $daily) {
            $image = BusMaintenanceImage::where('bus_maintenance_id','=', $daily->id)->get();
            $daily->images = $image;
        }


        $off_routines = BusMaintenance::where('status','=', 'off routine')->where('vehicle_id','=',$vehicle->id)->orderBy('created_at','desc')->get();
        foreach ($off_routines as $key => $daily) {
            $image = BusMaintenanceImage::where('bus_maintenance_id','=', $daily->id)->get();
            $daily->images = $image;
        }

        return view('maintenance.show', compact('dailys','routines','off_routines', 'vehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BusMaintenance  $busMaintenance
     * @return \Illuminate\Http\Response
     */
    public function edit(BusMaintenance $busMaintenance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BusMaintenance  $busMaintenance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BusMaintenance $busMaintenance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BusMaintenance  $busMaintenance
     * @return \Illuminate\Http\Response
     */
    public function destroy(BusMaintenance $busMaintenance)
    {
        //
    }

    public function getImages ($id) {
        $daily = BusMaintenance::find($id)->first();
        $image = BusMaintenanceImage::where('bus_maintenance_id','=', $id)->get();
        return response($image);
    }

}
