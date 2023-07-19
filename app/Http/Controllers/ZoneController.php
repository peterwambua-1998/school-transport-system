<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $zones = Zone::all();

        return view('zones.index', compact('zones'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('zones.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!$request->arrone && !$request->arrtwo) {
            return redirect()->back()->with('unsuccess','Please make zone geo fence');
        }

        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'price' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            $zone = new Zone();
            $zone->name = $request->title;
            $zone->description = $request->description;
            $zone->price = $request->price;
            $zone->oneway_price = $request->oneway_price;
            $zone->save();

            $coords = count($request->arrone);

            for ($i=0; $i < $coords; $i++) { 
                DB::table('zones_coordinates')->insert([
                    "zone_id" => $zone->id,
                    "corrdinates" => $request->arrone[$i]
                ]);

                DB::table('zones_coordinates')->insert([
                    "zone_id" => $zone->id,
                    "corrdinates" => $request->arrtwo[$i]
                ]);
            }
        });

        return redirect()->route('zones.index')->with('success', 'Record added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Zone  $zone
     * @return \Illuminate\Http\Response
     */
    public function show(Zone $zone)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Zone  $zone
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $zone = Zone::find(Crypt::decrypt($id));

        return view('zones.edit')->with('zone', $zone);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Zone  $zone
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'price' => 'required',
        ]);

        $zone = Zone::find($id);
        $zone->name = $request->title;
        $zone->description = $request->description;
        $zone->price = $request->price;
        $zone->oneway_price = $request->oneway_price;


        $routesZones = DB::table('route_zones')->where('zone_id','=', $zone->id)->get();

        foreach ($routesZones as $routesZone) {
            $rt = Route::where('id', '=', $routesZone->route_id)->first();
            $rt->price = $request->price;
            $rt->update();
        }

        if ($zone->update()) {
            return redirect()->route('zones.index')->with('success', 'Record added successfully');
        }

        return redirect()->back()->with('unsuccess', 'Sytem error please try again');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Zone  $zone
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $zone = Zone::find($id);
        $zone->status = 0;
        if ($zone->update()) {
            return redirect()->route('zones.index')->with(['success' => 'Record deactivated successfully']);
        }
        /*
        $routes = DB::table('route_zones')->where('zone_id', '=', $zone->id)->get();

        if ($routes->isNotEmpty()) {
            return redirect()->back()->with('unsuccess', 'Reassign routes that use this zone');
        } 
        DB::table('zones_coordinates')->where('zone_id','=',$zone->id)->delete();
        DB::table('route_zones')->where('zone_id', '=', $zone->id)->delete();
        if ($zone->delete()) {
            return redirect()->route('routes.index')->with('success', 'Record deleted successfully');
        }
        */
        return redirect()->back()->with('unsuccess', 'System error please try again');
    }

    public function activate(Request $request)
    {
        $zone = Zone::find($request->zone_id);
        $zone->status = 1;
        if ($zone->update()) {
            return redirect()->route('zones.index')->with(['success' => 'Record deactivated successfully']);
        }
    }

    /**
     * zone geofence page
     */
    public function zoneGeoFencePage($id)
    {
        $zone = Zone::find(Crypt::decrypt($id));

        $routes = DB::table('route_zones')->where('zone_id','=',$zone->id)->get();

        return view('zones.geofence', compact('zone','routes'));
    }

    /**
     * get zone geofence coordinates
     */
    public function getZoneGeoFenceCoords($id)
    {
        $zone = Zone::find($id);
        
        $coordinates = DB::table('zones_coordinates')->where('zone_id','=', $zone->id)->get();

        return response($coordinates);
    }

    /**
     * edit zone geofence page
     */
    public function zoneGeoFenceEdit($id)
    {
        $zone = Zone::find(Crypt::decrypt($id));
        
        return view('zones.edit-geofence', compact('zone'));
    }

    /**
     * upddate zone geofence details
     */
    public function updateZoneGeoFence(Request $request,$id)
    {
        if(!$request->arrone && !$request->arrtwo) {
            return redirect()->back()->with('unsuccess','Please make zone geo fence');
        }
        
        if (!$request->has('arrone')) {
            return redirect()->back()->with('unsuccess','Please add a valid geofence');
        }

        $zone = Zone::find(Crypt::decrypt($id));

        DB::table('zones_coordinates')->where('zone_id','=', $zone->id)->delete();

        $coords = count($request->arrone);

        for ($i=0; $i < $coords; $i++) { 
            DB::table('zones_coordinates')->insert([
                "zone_id" => $zone->id,
                "corrdinates" => $request->arrone[$i]
            ]);

            DB::table('zones_coordinates')->insert([
                "zone_id" => $zone->id,
                "corrdinates" => $request->arrtwo[$i]
            ]);
        }

        return redirect()->route('zoneGeoFencePage', Crypt::encrypt($zone->id))->with('success','Zone geofence updated');
    }

    public function zones()
    {
        $zones = Zone::all();

        $final_array = [];

        foreach ($zones as $zone) {
           $inner_array = ["zone" => "", "coordinates" => ""];

           $coordinates = DB::table('zones_coordinates')->where('zone_id','=', $zone->id)->get();

           $inner_array["zone"] = $zone;
           $inner_array["coordinates"] = $coordinates;

           array_push($final_array, $inner_array);
        }

        return response($final_array);
    }
}
