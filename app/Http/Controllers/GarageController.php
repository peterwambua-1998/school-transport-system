<?php

namespace App\Http\Controllers;

use App\Models\BusMaintenance;
use App\Models\Garage;
use App\Models\SchoolTermDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class GarageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $garages = Garage::all();
        return view('garage.index', compact('garages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('garage.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->lat && !$request->lng) {
            return redirect()->back()->with('unsuccess','Please specify garage location');
        }
        $request->validate([
            'name' => 'required',
            'location' => 'required',
            'contact_person' => 'required',
            'contact_phone' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'status' => 'required'
        ]);


        $garage = new Garage();
        $garage->name = $request->name;
        $garage->contact_person = $request->contact_person;
        $garage->contact_phone = $request->contact_phone;
        $garage->location = $request->location;
        $garage->lat = $request->lat;
        $garage->lng = $request->lng;
        $garage->active = $request->status;
        
        if ($garage->save()) {
            return redirect()->route('garage.index')->with('success', 'Record added successfully');
        }

        return redirect()->route('garage.index')->with('unsuccess', 'System error please try again later');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Garage  $garage
     * @return \Illuminate\Http\Response
     */
    public function show(Garage $garage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Garage  $garage
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $garage = Garage::find(Crypt::decrypt($id));

        return view('garage.edit', compact('garage'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Garage  $garage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!$request->lat && !$request->lng) {
            return redirect()->back()->with('unsuccess','Please specify garage location');
        }
        
        $request->validate([
            'name' => 'required',
            'location' => 'required',
            'contact_person' => 'required',
            'contact_phone' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'status' => 'required'
        ]);


        $garage = Garage::find($id);
        $garage->name = $request->name;
        $garage->contact_person = $request->contact_person;
        $garage->contact_phone = $request->contact_phone;
        $garage->location = $request->location;
        $garage->lat = $request->lat;
        $garage->lng = $request->lng;
        $garage->active = $request->status;
        
        if ($garage->update()) {
            return redirect()->route('garage.index')->with('success', 'Record updated successfully');
        }

        return redirect()->route('garage.index')->with('unsuccess', 'System please try again later');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Garage  $garage
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $garage = Garage::find($id);

        $maintenance = BusMaintenance::where('garage','=',$garage->id)->get();

        if ($maintenance) {
            DB::table('bus_maintenances')->where('garage','=',$garage->id)->delete();
        }
        $garage->active = 0;
        if ($garage->update()) {
            return redirect()->route('garage.index')->with('success', 'Record deactivated successfully');
        }
    }

    public function activate(Request $request)
    {
        $garage = Garage::find($request->garage_id);
        $garage->active = 1;
        if ($garage->update()) {
            return redirect()->route('garage.index')->with('success', 'Record deactivated successfully');
        }
    }
}
