<?php

namespace App\Http\Controllers;

use App\Models\DepatureChecklist;
use App\Models\ReturnChecklist;
use App\Models\SchoolAttendance;
use App\Models\SchoolTermDate;
use App\Models\SchoolTrip;
use App\Models\SchoolTripGrade;
use App\Models\Settings;
use App\Models\Student;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\MockObject\Stub\Stub;

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

        $vehicles = Vehicle::where('status','=',1)->get();

        $grades = DB::table('student_classes')->where('status','=',1)->get();


        return view('schooltrips.create')->with([
            'notifications' => $notifications,
            'teachers' => $teachers,
            'vehicles' => $vehicles,
            'grades' => $grades
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
        if ($request->paid_unpaid == 'select...') {
            return redirect()->back()->with('unsuccess','Please select school trip type.');
        }

        if ($request->grade == 'select...') {
            return redirect()->back()->with('unsuccess','Please select school trip grade.');
        }
        
        $request->validate([
            'trip_name' => 'required',
            'destination' => 'required'
        ]);

        $term = SchoolTermDate::where('status', '=', 1)->first();

        if (! $term) {
            return redirect()->back()->with('unsuccess', 'School terms have not been created');
        }

        $schoolTrip = new SchoolTrip();
        $schoolTrip->trip_name = $request->trip_name;
        $schoolTrip->status = $request->paid_unpaid;
        $schoolTrip->price = $request->price;
        $schoolTrip->trip_date = $request->trip_date;
        $schoolTrip->departure_time = $request->depature_time;
        $schoolTrip->return_time = $request->return_time;
        $schoolTrip->term_id = $term->id;
        $schoolTrip->has_more_destinations = $request->more_than_one_destion;
        $detinations = explode(',',$request->destination);


        $schoolTrip->save();

        //upload to firebase
        if ($request->user_type == 'driver') {
            //post to firebase
            // URL to send the POST request to
            $url = 'https://mfika.projtrac.co.ke/trips/create';

            // Data to send in the request
            $data = [
                'guid' => $schoolTrip->id,
                'trip_name' => $request->trip_name
            ];

            // Initialize cURL
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                $error_msg = curl_error($ch);
                Log::error($error_msg);
            }
            curl_close($ch);
        }

        //multiple grades
        for ($i=0; $i < count($request->grade); $i++) { 
            $trip_grades = new SchoolTripGrade();
            $trip_grades->schooltrip_id = $schoolTrip->id;
            $trip_grades->grade_id = $request->grade[$i];
            $trip_grades->save();
        }

        //mutiple teacher
        if($request->teacher_id) {

            if(count($request->teacher_id) > 0) {
                for ($i=0; $i < count($request->teacher_id); $i++) { 
                    DB::table('schooltrip_teacher')->insert([
                        "schooltrip_id" => $schoolTrip->id,
                        "teacher_id" => $request->teacher_id[$i],
                    ]);
                }
            }
        }

        
        //mutiple vehicles
        if($request->vehicle_id){
            if(count($request->vehicle_id) > 0) {
                for ($t=0; $t < count($request->vehicle_id); $t++) { 
                    DB::table('schooltrip_vehicle')->insert([
                        "schooltrip_id" => $schoolTrip->id,
                        "vehicle_id" => $request->vehicle_id[$t],
                    ]);
                }
            }
        }
        
        //mutiple destinations
        for ($z=0; $z < count($detinations); $z++) { 
            DB::table('school_trips_destinations')->insert([
                "destination" => $detinations[$z],
                "school_trip_id" => $schoolTrip->id
            ]);
        }

        if ($request->more_than_one_destion == 1) {
            return redirect()->route('schoolTripRouteMoreDests', $schoolTrip->id)->with(['success' => 'School trip saved successfully', 'schooltrip' => $schoolTrip]);
        }

        if ($request->more_than_one_destion == 0) {
            return redirect()->route('schooltriproute', $schoolTrip->id)->with(['success' => 'School trip saved successfully', 'schooltrip' => $schoolTrip]);
        }
        

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
        $schoolTrip = SchoolTrip::find(Crypt::decrypt($id));


        $vehicles = Vehicle::where('status','=',1)->get();

        $teachers = User::where('user_type', '=', 'teacher')->where('status','=',1)->get();

        $grades = DB::table('student_classes')->where('status','=',1)->get();

        $dests = DB::table('school_trips_destinations')->where('school_trip_id','=',$schoolTrip->id)->get();

        $destString = '';
        foreach ($dests as $key => $dest) {
            $destString .= ',' . $dest->destination;
        }
        
        $destString = ltrim($destString, ',');


        return view('schooltrips.edit')->with([
            'schooltrip' => $schoolTrip,
            'vehicles' => $vehicles,
            'teachers' => $teachers,
            'grades' => $grades,
            'dests' => $destString
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
            'trip_name' => 'required',
            'destination' => 'required'
        ]);

        $term = SchoolTermDate::where('status', '=', 1)->first();

        if (! $term) {
            return redirect()->back()->with('unsuccess', 'School terms have not been created');
        }

        $schoolTrip = SchoolTrip::find($id);
        $schoolTrip->trip_name = $request->trip_name;
        $schoolTrip->status = $request->paid_unpaid;
        
        $schoolTrip->trip_date = $request->trip_date;
        $schoolTrip->departure_time = $request->depature_time;
        $schoolTrip->return_time = $request->return_time;
        $detinations = explode(',',$request->destination);

        if ($request->paid_unpaid == 'paid') {
            $schoolTrip->price = $request->price;
        } 

        if ($request->paid_unpaid == 'unpaid') {
            $schoolTrip->price = 0;
        }

        //multiple grades
        DB::table('school_trip_grades')->where('schooltrip_id','=', $schoolTrip->id)->delete();
        for ($i=0; $i < count($request->grade); $i++) { 
            $trip_grades = new SchoolTripGrade();
            $trip_grades->schooltrip_id = $schoolTrip->id;
            $trip_grades->grade_id = $request->grade[$i];
            $trip_grades->save();
        }

        //multiple teacher
        if($request->teacher_id) {
            DB::table('schooltrip_teacher')->where('schooltrip_id','=', $schoolTrip->id)->delete();
            for ($i=0; $i < count($request->teacher_id); $i++) { 
                
                DB::table('schooltrip_teacher')->insert([
                    "schooltrip_id" => $schoolTrip->id,
                    "teacher_id" => $request->teacher_id[$i],
                ]);
            }
        }
        
        //multiple vehicles
        if($request->vehicle_id) {
            DB::table('schooltrip_vehicle')->where('schooltrip_id','=', $schoolTrip->id)->delete();
            for ($t=0; $t < count($request->vehicle_id); $t++) { 
                
                DB::table('schooltrip_vehicle')->insert([
                    "schooltrip_id" => $schoolTrip->id,
                    "vehicle_id" => $request->vehicle_id[$t],
                ]);
            }
        }
        
        //mutiple destinations
        DB::table('school_trips_destinations')->where('school_trip_id','=', $schoolTrip->id)->delete();
        for ($z=0; $z < count($detinations); $z++) { 
            
            DB::table('school_trips_destinations')->insert([
                "destination" => $detinations[$z],
                "school_trip_id" => $schoolTrip->id
            ]);
        }


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
    public function destroy($id)
    {
        $schoolTrip = SchoolTrip::find($id);

        $depatureChecklist = DepatureChecklist::where('schooltrip_id','=', $schoolTrip->id)->get();
        if ($schoolTrip->status == 'paid') {
            $payments = DB::table('school_trip_payment_tables')->where('schooltrip_id','=', $schoolTrip->id)->get();
            if ($payments->isNotEmpty()) {
                return redirect()->back()->with('unsuccess', 'School trip has payments against it.');
            }
        }
        if ($depatureChecklist->isNotEmpty()) {
            return redirect()->back()->with('unsuccess', 'School trip has registered students');
        }

        /*
        DB::table('schooltrip_vehicle')->where('schooltrip_id','=', $schoolTrip->id)->delete();
        DB::table('depature_checklists')->where('schooltrip_id','=', $schoolTrip->id)->delete();
        DB::table('schooltrip_teacher')->where('schooltrip_id','=', $schoolTrip->id)->delete();
        DB::table('school_trips_destinations')->where('school_trip_id','=', $schoolTrip->id)->delete();
        */
        $schoolTrip->active = 0; 
        if($schoolTrip->update()) {
            return redirect()->back()->with('success', 'Record deactivated successfully');
        }
    }

    public function activate(Request $request)
    {
        $schoolTrip = SchoolTrip::find($request->schooltrip_id);
        $schoolTrip->active = 1;
        if($schoolTrip->update()) {
            return redirect()->back()->with('success', 'Record activated successfully');
        }
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

        $schoolTrip->trip_route = $request->origin;
        $schoolTrip->destination = $request->destination;
        $schoolTrip->waypont_one = $request->waypoint_1;
        $schoolTrip->waypont_two = $request->waypoint_2;
        $schoolTrip->waypont_three = $request->waypoint_3;
        $schoolTrip->waypont_four = $request->waypoint_4;
        $schoolTrip->waypont_five = $request->waypoint_5;
        $schoolTrip->waypont_six = $request->waypoint_6;
        $schoolTrip->waypont_seven = $request->waypoint_7;
        $schoolTrip->waypont_eight = $request->waypoint_8;

        if ($request->has('destss')) {
            $schoolTrip->dest_app = $request->destss;
        }

        if ($schoolTrip->update()) {
            return redirect('/driver-myschooltrips/schooltrips/schooltripshow/'. $schoolTrip->id)->with('success','Record stored successfully');
        }

        return redirect()->back()->with('unsuccess', 'System error please try again later');
    }

    /**
     * edit page for route with no waypoints
    */
    public function editNoWayPoints($id)
    {
        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        $schoolTrip = SchoolTrip::find($id);

        $settings = Settings::find(1);

        if (! $settings) {
            return redirect()->route('settings.create')->with('unsuccess', 'Please register system settings');
        }

        return view('schooltrips.editroute')->with([
            'notifications' => $notifications,
            'schooltrip' => $schoolTrip,
            'settings' => $settings
        ]);
    }


     /**
     * edit page for route with waypoints
    */
    public function editWayPoints($id)
    {
    
        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        $schoolTrip = SchoolTrip::find($id);

        $settings = Settings::find(1);

        if (! $settings) {
            return redirect()->route('settings.create')->with('unsuccess', 'Please register system settings');
        }

        return view('schooltrips.editroutemoredest')->with([
            'notifications' => $notifications,
            'route' => $schoolTrip,
            'settings' => $settings,
            'schooltrip' => $schoolTrip
        ]);
    }

    /**
     * 
     * show school trip route
     * 
     */
    public function showRoutePath($id)
    {
        $schoolTrip = SchoolTrip::find($id);

        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        if ($schoolTrip->has_more_destinations == 1) {
            return view('schooltrips.routemoredestshow')->with([
                'route' => $schoolTrip,
                'notifications' => $notifications
            ]);
        } 

        if ($schoolTrip->has_more_destinations == 0) {
            return view('schooltrips.routedisplay')->with([
                'schoolTrip' => $schoolTrip,
                'notifications' => $notifications
            ]);
        }

       
    }


    /**
     * 
     * page to serve making of trip with many destinations
     */
    public function schoolTripRouteMoreDests($id)
    {
        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        $schoolTrip = SchoolTrip::find($id);

        $settings = Settings::find(1);

        if (! $settings) {
            return redirect()->route('settings.create')->with('unsuccess', 'Please register system settings');
        }

        return view('schooltrips.moredest')->with([
            'notifications' => $notifications,
            'schooltrip' => $schoolTrip,
            'settings' => $settings
        ]);
    }

    /**
     * 
     * page to serve making of trip with many destinations
     * 
     */
    public function saveSchoolTripManyDest(Request $request)
    {
        
    }

    /**
     * 
     * add student to school trip
     * 
     */
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
                $attendances = Student::where('grade', '=', $schoolTrip->grade)->get();

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

    /**
     * 
     * mark attendance school trip
     * 
     */
    public function markAttendance($id)
    {
        $depature = DepatureChecklist::where('schooltrip_id', '=', $id)->get();

        return view('school_attendance.markattendance')->with([
            'depature' => $depature
        ]);
    }
    
}
