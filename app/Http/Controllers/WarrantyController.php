<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Warranty;
use App\Models\WarrantyClaim;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class WarrantyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vehicles = Vehicle::where('status','=',1)->get();

        foreach ($vehicles as $key => $vehicle) {
            $warranty = Warranty::where('vehicle_id','=', $vehicle->id)->orderBy('created_at','desc')->get();
            $vehicle->warranties = $warranty;
        }
        return view('warranty.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $vehicle = Vehicle::find(Crypt::decrypt($id));

        return view('warranty.create', compact('vehicle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required',
            'status' => 'required'
        ]);

        if ($request->status == 'select...') {
            return redirect()->back()->with('unsuccess','Please select status');
        }

        $vehicle = Vehicle::find($request->vehicle_id);

        //vehicle warranty
        $veh_warranty = Warranty::where('vehicle_id','=', $vehicle->id)->where('type','=', 'vehicle')->where('status','=','active')->first();
        if ($veh_warranty && $request->type == "vehicle") {
            return redirect()->back()->with('unsuccess','Vehicle has warranty type vehicle');
        }

        //warranty parts
        $parts_warranty = Warranty::where('vehicle_id','=', $vehicle->id)->where('type','=', 'parts')->where('status','=','active')->first();
        if ($parts_warranty && $request->type == "parts") {
            return redirect()->back()->with('unsuccess','Vehicle has warranty type vehicle');
        }
        
        $warranty = new Warranty();
        $warranty->vehicle_id = $request->vehicle_id;
        $warranty->waranty_value = $request->waranty_value;
        $warranty->status = $request->status;
        $warranty->dealer = $request->dealer;
        $warranty->type = $request->type;
        $warranty->measurement = $request->measurement;
        if ($request->type == 'parts') {
            $warranty->warranty_parts = $request->parts_description;
        }

        if ($warranty->save()) {
            return redirect()->route('warranty.index')->with('success','Record added successfully');
        }

        return redirect()->back()->with('unsuccess','System error please try again');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Warranty  $warranty
     * @return \Illuminate\Http\Response
     */
    public function show(Warranty $warranty)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Warranty  $warranty
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $warranty = Warranty::find(Crypt::decrypt($id));

        $vehicle = Vehicle::find($warranty->vehicle_id);

        return view('warranty.edit', compact('warranty','vehicle'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Warranty  $warranty
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'vehicle_id' => 'required',
            'status' => 'required'
        ]);

        if ($request->status == 'select...') {
            return redirect()->back()->with('unsuccess','Please select status');
        }

        $warranty =  Warranty::find($id);
        $warranty->vehicle_id = $request->vehicle_id;
        $warranty->waranty_value = $request->waranty_value;
        $warranty->status = $request->status;
        $warranty->dealer = $request->dealer;
        $warranty->type = $request->type;
        $warranty->measurement = $request->measurement;
        if ($request->type == 'parts') {
            $warranty->warranty_parts = $request->parts_description;
        }
        if ($warranty->update()) {
            return redirect()->route('warranty.index')->with('success','Record updated successfully');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Warranty  $warranty
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // relationships: vehicle
        $warranty = Warranty::find($id);
        $warranty->status = 'inactive';
        if ($warranty->update()) {
            return redirect()->route('warranty.index')->with('success','Record deactivated successfully');
        }
    }

    public function activate(Request $request)
    {
        // relationships: vehicle
        $warranty = Warranty::find($request->warranty_id);
        $warranty->status = 'active';
        if ($warranty->update()) {
            return redirect()->route('warranty.index')->with('success','Record activated successfully');
        }
    }
    public function claims($id) 
    {
        $warranty = Warranty::find(Crypt::decrypt($id));
        $vehicle = Vehicle::find($warranty->vehicle_id);
        $claims = WarrantyClaim::where('warranty_id','=', $warranty->id)->get();
        return view('warranty.claims', compact('warranty','vehicle','claims'));
    }
}
