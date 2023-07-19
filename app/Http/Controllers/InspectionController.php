<?php

namespace App\Http\Controllers;

use App\Events\InspectionDate;
use App\Models\Inspection;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use stdClass;

class InspectionController extends Controller
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
            $inspection = Inspection::where('vehicle_id','=', $vehicle->id)->orderBy('created_at','desc')->first();
            if ($inspection) {
                $days_to_send_before_exp = Carbon::createFromFormat('Y-m-d', $inspection->next_inspection)->subDays(2);
                $today = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));

                if ($days_to_send_before_exp->eq($today) && $inspection->notification_send == 0) {
                    InspectionDate::dispatch($inspection);
                    $inspection->notification_send = 1;
                    $inspection->update();
                }
            }
            $vehicle->inspection = $inspection;
        }
        return view('inspection.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $vehicle = Vehicle::find(Crypt::decrypt($id));

        return view('inspection.create', compact('vehicle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!$request->lat && !$request->lng) {
            return redirect()->back()->with('unsuccess','Please select location for inspection');
        }

        if ($request->vehicle_id == 'select...') {
            return redirect()->back()->with('unsuccess','Please select vehicle');
        }
        
        $request->validate([
            'vehicle_id' => 'required',
        ]);

        $inspection = new Inspection();
        $inspection->vehicle_id = $request->vehicle_id;
        $inspection->last_inspection = $request->last_inspection;
        $inspection->next_inspection = $request->next_inspection;
        $inspection->location_name = $request->location_name;
        $inspection->lat = $request->lat;
        $inspection->lng = $request->lng;
        $inspection->status = 1;
        
        if ($inspection->save()) {
            return redirect()->route('inspection.index')->with('success', 'Record added successfully');
        }
        return redirect()->back()->with('unsuccess', 'System error please try again');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inspection  $inspection
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Inspection  $inspection
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $inspection = Inspection::find(Crypt::decrypt($id));

        $vehicle = Vehicle::find($inspection->vehicle_id);

        if ($inspection->report) {
            return redirect()->back()->with('unsuccess', 'Report has been submitted for this inspection');
        }
        
        return view('inspection.edit', compact('inspection','vehicle'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inspection  $inspection
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(!$request->lat && !$request->lng) {
            return redirect()->back()->with('unsuccess','Please select location for inspection');
        }
        
        $request->validate([
            'vehicle_id' => 'required',
        ]);

        if ($request->vehicle_id == 'select...') {
            return redirect()->back()->with('unsuccess','Please select vehicle');
        }

        $inspection = Inspection::find($id);
        $inspection->vehicle_id = $request->vehicle_id;
        $inspection->last_inspection = $request->last_inspection;
        $inspection->next_inspection = $request->next_inspection;
        $inspection->location_name = $request->location_name;
        $inspection->lat = $request->lat;
        $inspection->lng = $request->lng;
        if ($inspection->update()) {
            return redirect()->route('inspection.index')->with('success', 'Record updated successfully');
        }
        return redirect()->back()->with('unsuccess', 'System error please try again');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inspection  $inspection
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $inspection =  Inspection::find(Crypt::decrypt($id));
        $inspection->status = 0;
        if ($inspection->update()) {
            return redirect()->route('inspection.index')->with('success', 'Record deactivated successfully');
        }
        return redirect()->back()->with('unsuccess', 'System error please try again');
    }

    public function activate(Request $request)
    {
        $inspection =  Inspection::find($request->inspection_id);
        $inspection->status = 1;
        if ($inspection->update()) {
            return redirect()->route('inspection.index')->with('success', 'Record activated successfully');
        }
        return redirect()->back()->with('unsuccess', 'System error please try again');
    }
    
    public function saveInspectionReport(Request $request)
    {
        $request->validate([
            'image' => 'required'
        ]);

        $inspection =  Inspection::find(Crypt::decrypt($request->inspection_id));
        $path = $request->file('image')->store('inspection', 'public_uploads');
        $inspection->office_comment = $request->comment;
        $inspection->report = $path;

        if($inspection->update()){
            return redirect()->back()->with('success','Record added successfully');
        }
        return redirect()->back()->with('unsuccess', 'System error please try again');
    }

    public function downloadReport($id)
    {
        $inspection = Inspection::find(Crypt::decrypt($id));

        $path = Storage::disk('public_uploads')->path($inspection->report);
        $ext = pathinfo(Storage::disk('public_uploads')->path($inspection->report),PATHINFO_EXTENSION);
        $file_name = 'inspection-report-'.date('Ymdhis').'.'.$ext;
        return response()->download($path, $file_name);
    }
}
