<?php

namespace App\Http\Controllers\api\v1;

use App\Events\SchoolTripVehicle;
use App\Http\Controllers\Controller;
use App\Models\DepatureChecklist;
use App\Models\ReturnChecklist;
use App\Models\SchoolAttendance;
use App\Models\SchoolTermDate;
use App\Models\SchoolTrip;
use App\Models\Settings;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolTripController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $term = SchoolTermDate::where('status', '=', 1)->first();

        if (! $term) {
            return redirect()->route('term.create')->with('unsuccess', 'Please create term and make it active');
        }

        $schoolTrips = SchoolTrip::where('term_id', '=', $term->id)->orderby('created_at', 'DESC')->get();

        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        

        return view('schooltrips.index')->with([
            'schooltrips' => $schoolTrips,
            'notifications' => $notifications
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        $teachers = User::where('user_type', 'LIKE', 'teacher')->get();

        $vehicles = Vehicle::all();

        return view('schooltrips.create')->with([
            'notifications' => $notifications,
            'teachers' => $teachers,
            'vehicles' => $vehicles
        ]);
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
            'trip_name' => 'required',
            'teacher_id' => 'required'
        ]);

        $term = SchoolTermDate::where('status', '=', 1)->first();

        if (! $term) {
            return redirect()->back()->with('unsuccess', 'School terms have not been created');
        }

        $schoolTrip = new SchoolTrip();
        $schoolTrip->trip_name = $request->trip_name;
        $schoolTrip->teacher_id = $request->teacher_id;
        $schoolTrip->vehicle_id = $request->vehicle_id;
        $schoolTrip->status = $request->paid_unpaid;
        $schoolTrip->grade = $request->grade;
        $schoolTrip->price = $request->price;
        $schoolTrip->trip_date = $request->trip_date;
        $schoolTrip->departure_time = $request->depature_time;
        $schoolTrip->return_time = $request->return_time;
        $schoolTrip->term_id = $term->id;

        if($schoolTrip->save()) {
            return redirect()->route('schooltriproute', $schoolTrip->id)->with(['success' => 'School trip saved successfully', 'schooltrip' => $schoolTrip]);
        };

        return redirect()->back()->with('success', 'School trip saved successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SchoolTrip  $schoolTrip
     * @return \Illuminate\Http\Response
     */
    public function show(SchoolTrip $schoolTrip)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SchoolTrip  $schoolTrip
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $schoolTrip = SchoolTrip::find($id);

        $vehicles = Vehicle::all();

        $teachers = User::where('user_type', '=', 'teacher')->get();

        return view('schooltrips.edit')->with([
            'schooltrip' => $schoolTrip,
            'vehicles' => $vehicles,
            'teachers' => $teachers
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SchoolTrip  $schoolTrip
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'vehicle_id' => 'required',
            'trip_name' => 'required',
            'teacher_id' => 'required'
        ]);

        $term = SchoolTermDate::where('status', '=', 1)->first();

        if (! $term) {
            return redirect()->back()->with('unsuccess', 'School terms have not been created');
        }

        $schoolTrip = SchoolTrip::find($id);
        $schoolTrip->trip_name = $request->trip_name;
        $schoolTrip->teacher_id = $request->teacher_id;
        $schoolTrip->vehicle_id = $request->vehicle_id;
        $schoolTrip->status = $request->paid_unpaid;
        $schoolTrip->grade = $request->grade;
        $schoolTrip->price = $request->price;
        $schoolTrip->trip_date = $request->trip_date;
        $schoolTrip->departure_time = $request->depature_time;
        $schoolTrip->return_time = $request->return_time;


        if($schoolTrip->update()) {
            return redirect()->route('schooltrips.index')->with(['success' => 'School trip updated successfully', 'schooltrip' => $schoolTrip]);
        };

        return redirect()->back()->with('success', 'School trip saved successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SchoolTrip  $schoolTrip
     * @return \Illuminate\Http\Response
     */
    public function destroy(SchoolTrip $schoolTrip)
    {
        //
    }


    public function schooltriproute($id)
    {
        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        $schoolTrip = SchoolTrip::find($id);

        $settings = Settings::find(1);

        if (! $settings) {
            return redirect()->route('settings.create')->with('unsuccess', 'Please register system settings');
        }

        return view('schooltrips.route')->with([
            'notifications' => $notifications,
            'schooltrip' => $schoolTrip,
            'settings' => $settings
        ]);
    }

    public function saveRoutePath(Request $request)
    {
        $schoolTrip = SchoolTrip::find($request->schooltrip_id);

        $schoolTrip->trip_route = $request->source;
        $schoolTrip->destination = $request->destination;

        if ($schoolTrip->update()) {
            return redirect()->route('schooltrips.index')->with('success', 'School Trip Route Saved Successfully');
        }

        return redirect()->back()->with('unsuccess', 'System error please try again later');


    }


    public function showRoutePath($id)
    {
        $schoolTrip = SchoolTrip::find($id);

        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        return view('schooltrips.routedisplay')->with([
            'schoolTrip' => $schoolTrip,
            'notifications' => $notifications
        ]);
    }

    public function addStudents(Request $request)
    {
       
        $schoolTrip = SchoolTrip::find($request->trip_id);

        
        $date = date('Y-m-d');

        if ($schoolTrip->status == 'unpaid') {
            if ($schoolTrip->grade == 'general') {
                $attendances = SchoolAttendance::where('created_at', 'LIKE', '%'. $date . '%')->get();

                foreach ($attendances as $attendance) {
                    $depature = new DepatureChecklist();
                    $depature->schooltrip_id = $schoolTrip->id;
                    $depature->student_id = $attendance->student_id;
                    $depature->grade = $attendance->grade;
                    $depature->attendance = 'absent';
                    $depature->save();

                }

            } else {
                $attendances = SchoolAttendance::where('grade', '=', $schoolTrip->grade)->orWhere('created_at', 'LIKE', '%'. $date . '%')->get();

                foreach ($attendances as $attendance) {
                    $depature = new DepatureChecklist();
                    $depature->schooltrip_id = $schoolTrip->id;
                    $depature->student_id = $attendance->student_id;
                    $depature->grade = $attendance->grade;
                    $depature->attendance = 'absent';
                    $depature->save();

                }
            }
        }


        return redirect()->back()->with('success', 'students were added to depature checklist');
    }

    public function markAttendance($id)
    {
        $depature = DepatureChecklist::where('schooltrip_id', '=', $id)->get();

        return view('school_attendance.markattendance')->with([
            'depature' => $depature
        ]);
    }

    /**
     * update vehicle locaton
     */
    public function updateLocation($id, Request $request)
    {
        $vehicle = Vehicle::find($id);
        if (! $vehicle) {
            abort(404, 'not found');
        }

        $json = json_decode($request->getContent(), true);

        $vehicle->latitude = $json['lat'];
        $vehicle->longitude = $json['lng'];
        $vehicle->head = $json['head'];
        $vehicle->speed = $json['speed'];

        event(new SchoolTripVehicle($request->latitude, $request->longitude, $vehicle->id, $request->speed, $request->head));
    
        if($vehicle->update()) {
            return response(1);
        } else {
            return response(0);
        }
    }

    
}
