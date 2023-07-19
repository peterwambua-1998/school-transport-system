<?php

namespace App\Http\Controllers;

use App\Events\NewMessageNotification;
use App\Events\NewNotification;
use App\Events\VehicleLocation;
use App\Models\Attendance;
use App\Models\DepatureChecklist;
use App\Notifications\BusLate;
use App\Notifications\HereNotification;
use App\Notifications\SchoolTripDepatureNotification;
use App\Notifications\SchoolTripGoingBackNotification;
use App\Notifications\SchoolTripReachedDestNotification;
use App\Notifications\SchoolTripReachedSchoolNotification;
use App\Notifications\StartNotification;
use App\Notifications\StopNotification;
use App\Models\ReturnChecklist;
use App\Models\Route;
use App\Models\RoutePolyline;
use App\Models\SAndT;
use App\Models\SchoolTermDate;
use App\Models\SchoolTrip;
use App\Models\Student;
use App\Models\TermEvent;
use App\Models\TermHoliday;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Notification;

class DriverLoginContoller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $date = date('a');


        $vehicle = Vehicle::where('driver_id', '=', $user->id)->first();

        if ($vehicle == null) {
            return view('driver_login.novehicle');
        }

        if ($vehicle->trips) {
            $trips = $vehicle->trips;

            return view('driver_login.home')->with([
                'trips' => $trips,
                'date' => $date
            ]);
        }

        //return view('driver_login.home');
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
        $num = count($request->student_id);

        $amorpm = date('a');
        for ($i=0; $i < $num; $i++) { 
            # code...
    
            $attendance = new Attendance();
            $attendance->vehicle_id = $request->vehicle_id[$i];
            $attendance->student_id = $request->student_id[$i];
            $attendance->present = $request->present[$i];
            $attendance->route_time = $amorpm;
            $attendance->date = date('Y-m-d');

            $student = Student::find($request->student_id[$i]);
            $attendance->grade = $student->grade;

            $attendance->save();
        }
        

        

        return redirect()->route('driverlogin_home')->with([
            'success' => 'attendance was stored successfully',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getStudents(Request $request)
    {
        $user = Auth::user();

       

        $students = [];
        
        $finalstd = [];
        
        $sandt = SAndT::where('trip_id', '=', $request->trip_id)->get();

        

        foreach ($sandt as $s) {
            $students[] = Student::where('id', '=', $s->student_id)->get();
        }
        for ($i=0; $i < count($students); $i++) { 
            if (!empty($students[$i][0])) {
                array_push($finalstd, $students[$i][0]);
            }  
        }

        foreach($finalstd as $key => $final) {
            $parent_phone = User::where('id', '=', $final->parent_id)->first()->phone_num;

            $finalstd[$key]['parent_phone'] = $parent_phone;

        }

        $date = date('a');
        
        return view('driver_login.attendance')->with(['students' => $finalstd, 'date' => $date]);
    }

    public function notificationList()
    {
        $driver = Auth::user();

       

        $vehicle_id = Vehicle::where('driver_id', '=', $driver->id)->first()->id;
        
        $students = Student::where('vehicle_id', '=', $vehicle_id)->get();

       
        return view('driver_login.notifications')->with([
            'students' => $students,
            'vehicle' => $vehicle_id
        ]);
    }

    public function myStudents()
    {
        $driver = Auth::user();

       

        $vehicle_id = Vehicle::where('driver_id', '=', $driver->id)->first()->id;
        
        $students = Student::where('vehicle_id', '=', $vehicle_id)->get();

        return view('driver_login.mystudents')->with([
            'students' => $students
        ]);
    }

    public function confirmPuckupPage($id)
    {
        $student = Student::where('id', '=', Crypt::decrypt($id))->first();

        if (! $student) {
            return redirect()->back()->with('unsuccess', 'Student not found');
        }

        return view('driver_login.confirmpickup')->with([
            'student' => $student
        ]);
    }

    public function saveConfirmation(Request $request)
    {
       $student = Student::find($request->student_id);

       $student->confirm_pickup_driver = 1;

       if($student->update()) {
        return redirect()->route('driverlogin_mystudents')->with('success', 'Pickup/Drop off confirmed');
       }

       return redirect()->back()->with('unsuccess', 'System error please try again later');

    }

    public function getRoutePath($id)
    {
        $vehicle = Vehicle::where('driver_id', '=', $id)->first();

        if (! $vehicle) {
            return redirect()->back()->with('unsuccess', 'System error please try again later');
        }

        $route = RoutePolyline::where('route_id', '=', $vehicle->route_id)->first();

        if (! $route) {
            return redirect()->back()->with('unsuccess', 'System error please try again later');
        }

        return view('driver_login.route')->with([
            'route' => $route
        ]);
    }


    public function notifyHere(Request $request)
    {
        $student = Student::find($request->student_id);
        if (! $student) {
            return response(0);
        }


        $parent = User::where('id', '=', $student->parent_id)->first();

        if (! $parent) {
            return response(0);
        }

        Notification::send($parent, new HereNotification());

        event(new NewMessageNotification($parent->id, "Pickup/Dropoff for $student->first_name", 'Vevhicle is at your stop'));

        return response(1);
    }

    public function sendLateNotification(Request $request)
    {
        $driver = Auth::user();

        $vehicle = Vehicle::where('driver_id', '=', $driver->id)->first();

        $students = Student::where('vehicle_id', '=', $vehicle->id)->get();

        foreach ($students as $student) {
            $parent = User::where('id', '=', $student->parent_id)->first();

            Notification::send($parent, new BusLate($driver->name, $vehicle->plate_num, $vehicle->title, $driver->phone_num));

            event(new NewMessageNotification($parent->id, 'Bus Late', "Bus for $student->first_name $student->last_name will be late. Vehicle $vehicle->title ($vehicle->plate_num), Driver Name: $driver->name, Contact: $driver->phone_num"));
        }

        return response(1);
    }

    public function notifyStop(Request $request)
    {
        $users = User::where('user_type', 'LIKE', 'office staff')
                        ->orWhere('user_type', 'LIKE', 'admin')
                        ->orWhere('user_type', 'LIKE', 'supervisor')
                        ->orWhere('user_type', 'LIKE', 'manager')
                        ->orWhere('user_type', 'LIKE', 'office_executive')
                        
                        ->get();

        $driver = Auth::user();

        $vehicle = Vehicle::where('driver_id', '=', $driver->id)->first();

        Notification::send($users, new StopNotification($driver->name, $vehicle->plate_num, $vehicle->title, $driver->phone_num));

        $students = Student::where('vehicle_id', '=', $vehicle->id)->get();

        foreach ($students as $student) {
            $parent = User::where('id', '=', $student->parent_id)->first();

            Notification::send($parent, new StopNotification($driver->name, $vehicle->plate_num, $vehicle->title, $driver->phone_num));

        }

        return response(0);

    }

    public function mySchoolTrips()
    {
        $term = SchoolTermDate::where('status', '=', 1)->first();

        if (! $term) {
            return redirect()->back()->with('unsuccess', 'School terms have not been created');
        }

        $driver = Auth::user();

        $vehicle = Vehicle::where('driver_id', '=', $driver->id)->first();
        

        $schoolTrips = SchoolTrip::where('vehicle_id', '=', $vehicle->id)->where('term_id', '=', $term->id)->get();

        return view('drivers.myschooltrips')->with([
            'schooltrips' => $schoolTrips,
            
        ]);
    }

    public function saveApproval(Request $request)
    {
        
        $schooltrips = SchoolTrip::find($request->schooltrip_id);
        $schooltrips->route_changed = 1;
        if($schooltrips->update()) {
            return response('aprroval has been sent');
        }

        return response('System error please try gain');


    }


    public function sendStartSchoolTrips(Request $request)
    {
        
        $schooltrips = SchoolTrip::find($request->schooltrip_id);

        $depatures = DepatureChecklist::where('schooltrip_id', '=', $schooltrips->id)->get();

        foreach ($depatures as $depature) {
            $students = Student::where('id', '=', $depature->student_id)->get();
             foreach ($students as $student) {
                $parent = User::where('id', '=', $student->parent_id)->first();

                Notification::send($parent, new SchoolTripDepatureNotification());

                event(new NewMessageNotification($parent->id, "Start $schooltrips->trip_name School Trip", "$student->first_name has departed from school for $schooltrips->trip_name School Trip"));
            }
            
        }

        return response('Depature notification sent');
    }

    public function notifyStart()
    {
        
        $driver = Auth::user();

        

        $vehicle = Vehicle::where('driver_id', '=', $driver->id)->first();

        $students = Student::where('vehicle_id', '=', $vehicle->id)->get();

        

        foreach ($students as $student) {

            $user = User::where('id', '=',$student->parent_id)->first();
            
            Notification::send($user, new StartNotification($driver->name, $vehicle->plate_num, $vehicle->title, $driver->phone_num));
        }

        return response(['msg'=>'notification sent']);
    }

    public function schoolEvents()
    {
        $schoolterm = SchoolTermDate::where('status', '=', 1)->first();

        $terms = TermEvent::where('term_id', '=', $schoolterm->id)->get();

        return view('driverlogin.term_events')->with([
            'terms' => $terms
        ]);
    }

    public function schoolHolidays()
    {
        $user = Auth::user();
        
        $term = SchoolTermDate::where('status', '=', 1)->first();

        if (!$term) {
            return redirect()->back()->with('unsuccess', 'Terms have not been created');
        }

        $terms = TermHoliday::where('term_id', '=', $term->id)->get();

        
        
            return view('driver_login.school_holiday')->with([
                'terms' => $terms,
            ]);
        
    }


    public function sendReachedDestination($id)
    {
        $user = Auth::user();

        $schooltrips = SchoolTrip::find($id);

        $depatures = DepatureChecklist::where('schooltrip_id', '=', $id)->get();

        foreach ($depatures as $depature) {
            $students = Student::where('id', '=', $depature->student_id)->get();
             foreach ($students as $student) {
                $parent = User::where('id', '=', $student->parent_id)->first();

                Notification::send($parent, new SchoolTripReachedDestNotification());

                event(new NewMessageNotification($parent->id, "Reached destination $schooltrips->trip_name", "$student->first_name has reached  $schooltrips->destination_name"));
            }
            
        }

        return response('notification sent');
    }


    public function sendGoindBack($id)
    {
        $user = Auth::user();

        $schooltrips = SchoolTrip::find($id);

        $depatures = ReturnChecklist::where('schooltrip_id', '=', $id)->get();

        foreach ($depatures as $depature) {
            $students = Student::where('id', '=', $depature->student_id)->get();
             foreach ($students as $student) {
                $parent = User::where('id', '=', $student->parent_id)->first();

                Notification::send($parent, new SchoolTripGoingBackNotification());

                event(new NewMessageNotification($parent->id, "Going back from schoo trip", "$student->first_name is going back to school from schoo trip"));
            }
            
        }

        return response('notification sent');
    }

    public function sendReachedSchool($id)
    {
        $user = Auth::user();

        $depatures = ReturnChecklist::where('schooltrip_id', '=', $id)->get();

        foreach ($depatures as $depature) {
            $students = Student::where('id', '=', $depature->student_id)->get();
             foreach ($students as $student) {
                $parent = User::where('id', '=', $student->parent_id)->first();

                Notification::send($parent, new SchoolTripReachedSchoolNotification());

                event(new NewMessageNotification($parent->id, "Reached school from schoo trip", "$student->first_name has arrived in school from schoo trip"));
            }
            
        }

        return response('notification sent');
    }


    public function saveCoords(Request $request)
    {
        $driver = Auth::user();

        if ($driver->user_type != 'driver') {
            return response(['msg'=>'user is not driver']);
        }

        $vehicle = Vehicle::where('driver_id', '=', $driver->id)->first();

        $vehicle->latitude = $request->latitude;

        $vehicle->longitude = $request->longitude;

        event(new VehicleLocation($request->latitude, $request->longitude, $vehicle->id, $vehicle->speed, $vehicle->head));
        
        if($vehicle->update()) {
            return response(['msg' => 'updated']);
        } else {
            return abort(404);
        }
    }
    
}
