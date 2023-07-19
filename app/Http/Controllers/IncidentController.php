<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentImages;
use App\Models\Student;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IncidentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $incidentStds = Incident::where('caused_by', '=', 'student')->get();
        $incidentDrs = Incident::where('caused_by', '=', 'driver')->get();
        $incidentPts = Incident::where('caused_by', '=', 'parent')->get();
        $incidentAts = Incident::where('caused_by', '=', 'attendant')->get();

        return view('incidents.index', compact('incidentStds', 'incidentDrs', 'incidentPts', 'incidentAts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $vehicles = Vehicle::all();
        return view('incidents.create', compact('vehicles'));
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
            'source' => 'required'
        ]);

        $incident = new Incident();
        $incident->vehicle_id = $request->vehicle;
        $incident->source = $request->source;
        $incident->trip = $request->trip;
        $incident->date = $request->date;
        $incident->type = $request->type;
        $incident->caused_by = $request->caused_by;
        $incident->description = $incident->description;
        if ($request->has('image')) {
            $path = $request->file('image')->store('incidents', 'public_uploads');
            $incident->image = $path;
        }
        switch ($request->caused_by) {
            case 'student':
                $incident->student_assulter = $request->assulter;
                break;

            default:
                $incident->user_assulter = $request->assulter;
                break;
        }

        if ($incident->save()) {
            return redirect()->route('incidents.index')->with('success', 'Record added successfully');
        }
        return redirect()->route('incidents.index')->with('unsuccess', 'System error please try again');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Incident  $incident
     * @return \Illuminate\Http\Response
     */
    public function show(Incident $incident)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Incident  $incident
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $incident = Incident::find($id);
        return view('incidents.edit', compact('incident'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Incident  $incident
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'source' => 'required'
        ]);

        $incident = Incident::find($id);
        $incident->user_id = $request->user_id;
        $incident->bus_reg_no = $request->bus_reg_no;
        $incident->source = $request->source;
        $incident->trip = $request->trip;
        $incident->date = $request->date;
        $incident->type = $request->type;
        $incident->caused_by = $request->caused_by;
        if ($request->has('image')) {
            if ($incident->image) {
                Storage::disk('public_uploads')->delete($incident->image);
            }
            $path = $request->file('image')->store('incidents', 'public_uploads');
            $incident->image = $path;
        }
        switch ($request->caused_by) {
            case 'student':
                $incident->caused_by = $request->assulter;
                break;

            default:
                $incident->caused_by = $request->assulter;
                break;
        }

        if ($incident->save()) {
            return redirect()->route('incidents.index')->with('success', 'Record added successfully');
        }
        return redirect()->route('incidents.index')->with('unsuccess', 'System error please try again');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Incident  $incident
     * @return \Illuminate\Http\Response
     */
    public function destroy(Incident $incident)
    {
        //
    }

    /**
     * get users or students based on caused by
     */
    public function getCausedBy(Request $request)
    {
        $caused_by = $request->caused_by;

        if ($caused_by == 'student') {
            return response(Student::all());
        }

        if ($caused_by == 'attendant') {
            return response(User::where('user_type','=','attendant')->get());
        }

        if ($caused_by == 'parent') {
            return response(User::where('user_type','=','parent')->get());
        }

        if ($caused_by == 'driver') {
            return response(User::where('user_type','=','driver')->get());
        }
    }


    public function getVehicleTrips($id)
    {
        $vehicle = Vehicle::find($id);

        $trips = Trip::where('vehicle_id','=',$vehicle->id)->get();

        return response($trips);
    }

    public function incidentImages($id)
    {
        $incident = Incident::find($id);
        $images = IncidentImages::where('incident_id','=', $incident->id)->get();
        return response($images);
    }
}
