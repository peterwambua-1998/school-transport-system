<?php

namespace App\Http\Controllers\api\v1;

use App\Models\Attendance;
use App\Http\Controllers\Controller;
use App\Models\DepatureChecklist;
use App\Models\PickupPoint;
use App\Models\PickupPointStudent;
use App\Models\ReturnChecklist;
use App\Models\Route;
use App\Models\RoutePolyline;
use App\Models\SchoolTermDate;
use App\Models\SchoolTrip;
use App\Models\StandinBus;
use App\Models\StandinDriver;
use App\Models\Student;
use App\Models\TermEvent;
use App\Models\TermHoliday;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\SchoolTripDepatureNotification;
use App\Notifications\SchoolTripGoingBackNotification;
use App\Notifications\SchoolTripReachedDestNotification;
use App\Notifications\SchoolTripReachedSchoolNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use stdClass;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        
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
        $json = json_decode($request->getContent(), true);


        $user = auth('api')->user();

        $vehicle = Vehicle::where('attendant_id','=',$user->id)->first() ?? Vehicle::where('driver_id','=',$user->id)->first();
        //vehicle stand-in
        $check_stand_in = StandinBus::where('stand_in_vehicle','=', $vehicle->id)->where('status','=', 1)->first();

        if ($user->user_type == "driver" && $check_stand_in) {
            $vehicle = Vehicle::find($check_stand_in->original_vehicle);
        }
        
        //driver stand-in
        if ($user->user_type == "driver") {
            $check_stand_in_driver = StandinDriver::where('stand_in_driver','=', $user->id)->where('status','=', 1)->first();
            if ($check_stand_in_driver) {
                $vehicle = Vehicle::where('id', '=', $check_stand_in_driver->stand_in_vehicle)->first();
            }
        }

        //attendant stand-in
        if ($user->user_type == "attendant") {
            $check_stand_in_attendant = StandinDriver::where('stand_in_attendant','=', $user->id)->where('status','=', 1)->first();
            if ($check_stand_in_attendant) {
                $vehicle = Vehicle::where('id', '=', $check_stand_in_attendant->stand_in_vehicle)->first();
            }
        }

        if (! $vehicle) {
            return response('not authorized');
        }

        $students =  $json["students"];
        $num = count($students);
        $pickup = PickupPoint::find($json['pickup_id']);
        $pickupstudents = PickupPointStudent::where('pickuppoint_id','=',$pickup->id)->get();
        $trip = Trip::find($json['trip_id']);
        for ($i=0; $i < $num; $i++) { 
            $check = Attendance::where('student_id','=',$students[$i]["id"])->where('date','=',date('Y-m-d'))->where('route_time','=',$trip->time)->first();
            if ($check) {
                $check->delete();
            }
            $attendance = new Attendance();
            $attendance->vehicle_id = $vehicle->id;
            $attendance->student_id = $students[$i]["id"];
            if ($students[$i]["present"] == true) {
                $attendance->present = 'present';
            }else {
                $attendance->present = 'absent';
            }
            $attendance->route_time = $trip->time;
            $attendance->date = date('Y-m-d');
            $student = Student::find($students[$i]["id"]);
            $attendance->grade = $student->grade;
            $attendance->stream = $student->stream;
            $attendance->trip_id = $trip->id;
            $attendance->save();
        }

        return response(['msg'=>'data added']);
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

    //vehicle route path
    public function getRoutePath()
    {
        $driver = auth('api')->user();

        $vehicle = Vehicle::where('driver_id', '=', $driver->id)->first() ?? Vehicle::where('attendant_id', '=', $driver->id)->first();

        if (! $vehicle) {
            return abort(404, 'not found');
        }
        $check_stand_in = StandinBus::where('stand_in_vehicle','=', $vehicle->id)->where('status','=', 1)->first();
        
        if ($driver->user_type == "driver" && $check_stand_in) {
            $vehicle = Vehicle::find($check_stand_in->original_vehicle);
        }
        
        //driver stand-in
        if ($driver->user_type == "driver") {
            $check_stand_in_driver = StandinDriver::where('stand_in_driver','=', $driver->id)->where('status','=', 1)->first();
            if ($check_stand_in_driver) {
                $vehicle = Vehicle::where('id', '=', $check_stand_in_driver->stand_in_vehicle)->first();
            }
        }

        //attendant stand-in
        if ($driver->user_type == "attendant") {
            $check_stand_in_attendant = StandinDriver::where('stand_in_attendant','=', $driver->id)->where('status','=', 1)->first();
            if ($check_stand_in_attendant) {
                $vehicle = Vehicle::where('id', '=', $check_stand_in_attendant->stand_in_vehicle)->first();
            }
        }

        $vehicle_routes = DB::table('vehicle_routes')->where('vehicle_id','=', $vehicle->id)->get();

        $final_array = [];

        if ($vehicle_routes->isNotEmpty()) {
            $trips = Trip::where('vehicle_id','=', $vehicle->id)->get();
            foreach ($trips as $key => $trip) {
                $rt = Route::where('id','=',$trip->route_id)->first();
                $route = RoutePolyline::where('route_id', '=', $rt->id)->first();
                $route->trip  = $trip;
                array_push($final_array, $route);
            }
        }
        return response($final_array);
    }

    public function mySchoolTrips()
    {
        $term = SchoolTermDate::where('status', '=', 1)->first();

        if (! $term) {
            return abort(404, 'not found');
        }

        $driver = auth('api')->user();

        $vehicle = Vehicle::where('driver_id', '=', $driver->id)->first() ?? Vehicle::where('attendant_id', '=', $driver->id)->first();

        $check_stand_in = StandinBus::where('stand_in_vehicle','=', $vehicle->id)->where('status','=', 1)->first();
        if ($driver->user_type == "driver" && $check_stand_in) {
            $vehicle = Vehicle::find($check_stand_in->original_vehicle);
        }
        
        //driver stand-in
        if ($driver->user_type == "driver") {
            $check_stand_in_driver = StandinDriver::where('stand_in_driver','=', $driver->id)->where('status','=', 1)->first();
            if ($check_stand_in_driver) {
                $vehicle = Vehicle::where('id', '=', $check_stand_in_driver->stand_in_vehicle)->first();
            }
        }

        //attendant stand-in
        if ($driver->user_type == "attendant") {
            $check_stand_in_attendant = StandinDriver::where('stand_in_attendant','=', $driver->id)->where('status','=', 1)->first();
            if ($check_stand_in_attendant) {
                $vehicle = Vehicle::where('id','=', $check_stand_in_attendant->stand_in_vehicle)->first();
            }
        }

        return response($vehicle);
        
        $schoolTrips = DB::table('schooltrip_vehicle')->where('vehicle_id','=', $vehicle->id)->get();

        $schooltrips_array = [];

        foreach ($schoolTrips as $tr) {
            $trip = SchoolTrip::where('id', '=', $tr->schooltrip_id)->where('term_id', '=', $term->id)->first();
            $teachers = DB::table('schooltrip_teacher')->where('schooltrip_id','=', $trip->id)->get();
            $dests = DB::table('school_trips_destinations')->where('school_trip_id','=', $trip->id)->get()->pluck('destination');

            if (!$trip->has_more_destinations) {
                $origin = '('.$trip->trip_route .')';
                $dest = $trip->dest_app;
            } else {
                $origin = $trip->trip_route;
                $dest = $trip->destination;
            }

            $inner_array = [
                "id" => $trip->id,
                "trip_name" => $trip->trip_name,
                "teacher_name" => [],
                "teacher_phoner" => [],
                "has_more_destinations" => $trip->has_more_destinations,
                "destination_name" => $dests,
                "orgin_lat_lng" => $origin,
                "destination_lat_lng" => $dest,
                "waypont_one" => $trip->waypont_one,
                "waypont_two" => $trip->waypont_two,
                "waypont_three" => $trip->waypont_three,
                "waypont_four" => $trip->waypont_four,
                "waypont_five" => $trip->waypont_five,
                "waypont_six" => $trip->waypont_six,
                "waypont_seven" => $trip->waypont_seven,
                "waypont_eight" => $trip->waypont_eight,
                "status" => $trip->status,
                "grade" => $trip->grade,
                "price" => $trip->price,
                "trip_date" => $trip->trip_date,
                "departure_time" => $trip->departure_time,
                "return_time" => $trip->return_time,
                "route_changed" => $trip->route_changed,
                "approved" => $trip->approved,
                "term_id" => $trip->term_id,
            ];

            foreach ($teachers as $key => $teacher) {
                $tcher = User::where('id','=',$teacher->teacher_id)->first();
                $inner_array["teacher_name"][$key] = $tcher->name; 
                $inner_array["teacher_phoner"][$key] = $tcher->phone_num; 
            }

           
            array_push($schooltrips_array, $inner_array);
        }

        return response($schooltrips_array);
    }

    //when route changes
    public function saveApproval($id)
    {
        
        $schooltrips = SchoolTrip::find($id);

        if (! $schooltrips) {
            return abort(404,'not found');
        }
        $schooltrips->route_changed = 1;
        if($schooltrips->update()) {
            return response('approval has been sent');
        }

        return response('System error please try gain');
    }

    //for starting school trip
    public function sendStartSchoolTrips($id)
    {
        
        $schooltrips = SchoolTrip::find($id);

        if (! $schooltrips) {
            return abort(404,'not found');
        }

        $depatures = DepatureChecklist::where('schooltrip_id', '=', $schooltrips->id)->get();

        foreach ($depatures as $depature) {
            $students = Student::where('id', '=', $depature->student_id)->get();
             foreach ($students as $student) {
                $parent = User::where('id', '=', $student->parent_id)->first();

                Notification::send($parent, new SchoolTripDepatureNotification($schooltrips->trip_name));
            }
            
        }

        return response('Depature notification sent');
    }


    public function schoolEvents()
    {
        $schoolterm = SchoolTermDate::where('status', '=', 1)->first();

        if (! $schoolterm) {
            return abort(404,'not found');
        }

        $terms = TermEvent::where('term_id', '=', $schoolterm->id)->get();

        return view('driverlogin.term_events')->with([
            'terms' => $terms
        ]);
    }

    public function schoolHolidays()
    {
        $user = auth('api')->user();
        
        $term = SchoolTermDate::where('status', '=', 1)->first();

        if (! $term) {
            return abort(404,'not found');
        }

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
        $user = auth('api')->user();

        $depatures = DepatureChecklist::where('schooltrip_id', '=', $id)->get();

        foreach ($depatures as $depature) {
            $students = Student::where('id', '=', $depature->student_id)->get();
             foreach ($students as $student) {
                $parent = User::where('id', '=', $student->parent_id)->first();

                Notification::send($parent, new SchoolTripReachedDestNotification());
            }
            
        }

        return response('notification sent');
    }


    public function sendGoindBack($id)
    {
        $user = auth('api')->user();

        $depatures = ReturnChecklist::where('schooltrip_id', '=', $id)->get();

        foreach ($depatures as $depature) {
            $students = Student::where('id', '=', $depature->student_id)->get();
             foreach ($students as $student) {
                $parent = User::where('id', '=', $student->parent_id)->first();

                Notification::send($parent, new SchoolTripGoingBackNotification());
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
            }
            
        }

        return response('notification sent');
    }


    //all students
    public function myStudents()
    {
        $driver = auth('api')->user();

        $vehicle = Vehicle::where('driver_id', '=', $driver->id)->first();

        $check_stand_in = StandinBus::where('stand_in_vehicle','=', $vehicle->id)->where('status','=', 1)->first();
        
        if ($driver->user_type == "driver" && $check_stand_in) {
            $vehicle = Vehicle::find($check_stand_in->original_vehicle);
        }
        
        //driver stand-in
        if ($driver->user_type == "driver") {
            $check_stand_in_driver = StandinDriver::where('stand_in_driver','=', $driver->id)->where('status','=', 1)->first();
            if ($check_stand_in_driver) {
                $vehicle = Vehicle::where('id', '=', $check_stand_in_driver->stand_in_vehicle)->first();
            }
        }

        //attendant stand-in
        if ($driver->user_type == "attendant") {
            $check_stand_in_attendant = StandinDriver::where('stand_in_attendant','=', $driver->id)->where('status','=', 1)->first();
            if ($check_stand_in_attendant) {
                $vehicle = Vehicle::where('id', '=', $check_stand_in_attendant->stand_in_vehicle)->first();
            }
        }

        $veh_ch_am = DB::table('vehicle_students')->where('vehicle_id','=', $vehicle->id)->where('route_time','=', 'am')->get();
        $veh_ch_pm = DB::table('vehicle_students')->where('vehicle_id','=', $vehicle->id)->where('route_time','=', 'pm')->get();
       

        $students_array = ["am" => [], "pm" => []];

        foreach ($veh_ch_am as  $vh) {
            $child = Student::where('id','=',$vh->student_id)->first();
            if ($child->image) {
                $image = public_path('store/'.$child->image);

                $imageBase64 = "data:image/png;base64,".base64_encode(file_get_contents($image));
            }

            $inner_array = [
                'id' => $child->id,
                'vehicle_id' => $child->vehicle_id ?? '',
                'parent_name' => User::where('id','=',$child->parent_id)->first()->name ?? '',
                'parent_phone' => User::where('id','=',$child->parent_id)->first()->phone_num ?? '',
                'first_name' => $child->first_name ?? '',
                'last_name' => $child->last_name ?? '',
                'grade' => $child->grade ?? '',
                'parent_two' => $child->parent_two ?? '',
                'other' => $child->other ?? '',
                'lat' => $child->lat,
                'lng' => $child->lng,
                'image' => $imageBase64 ?? NULL
                
            ];

            array_push($students_array["am"], $inner_array);

        }

        foreach ($veh_ch_pm as  $vh) {
            $child = Student::where('id','=',$vh->student_id)->first();
            if ($child->image) {
                $image = public_path('store/'.$child->image);

                $imageBase64 = "data:image/png;base64,".base64_encode(file_get_contents($image));
            }

            $inner_array = [
                'id' => $child->id,
                'vehicle_id' => $child->vehicle_id ?? '',
                'parent_name' => User::where('id','=',$child->parent_id)->first()->name ?? '',
                'parent_phone' => User::where('id','=',$child->parent_id)->first()->phone_num ?? '',
                'first_name' => $child->first_name ?? '',
                'last_name' => $child->last_name ?? '',
                'grade' => $child->grade ?? '',
                'parent_two' => $child->parent_two ?? '',
                'other' => $child->other ?? '',
                'lat' => $child->lat_drop,
                'lng' => $child->lng_drop,
                'image' => $imageBase64 ?? NULL
            ];

            array_push($students_array["pm"], $inner_array);

        }

        return response($students_array);
    }

    //save confirmation
    //$id == student id
    public function saveConfirmation($id)
    {
       $student = Student::find($id);

        if (! $student) {
            return abort(404,'not found');
        }

       $student->confirm_pickup_driver = 1;

       if($student->update()) {
        return response('Pickup and Drop off confirmed');
       }

       return response('System error please try again');
    }

    //all students
    public function studentParentInfo()
    {
        $driver = auth('api')->user();

        $vehicle = Vehicle::where('driver_id', '=', $driver->id)->first();

        $check_stand_in = StandinBus::where('stand_in_vehicle','=', $vehicle->id)->where('status','=', 1)->first();
        
        if ($driver->user_type == "driver" && $check_stand_in) {
            $vehicle = Vehicle::find($check_stand_in->original_vehicle);
        }
        
        //driver stand-in
        if ($driver->user_type == "driver") {
            $check_stand_in_driver = StandinDriver::where('stand_in_driver','=', $driver->id)->where('status','=', 1)->first();
            if ($check_stand_in_driver) {
                $vehicle = Vehicle::where('id', '=', $check_stand_in_driver->stand_in_vehicle)->first();
            }
        }

        //attendant stand-in
        if ($driver->user_type == "attendant") {
            $check_stand_in_attendant = StandinDriver::where('stand_in_attendant','=', $driver->id)->where('status','=', 1)->first();
            if ($check_stand_in_attendant) {
                $vehicle = Vehicle::where('id', '=', $check_stand_in_attendant->stand_in_vehicle)->first();
            }
        }

        if (! $vehicle) {
            return abort(404, 'Vehicle not found or driver not allocated vehicle');
        }

        $veh_ch_am = DB::table('vehicle_students')->where('vehicle_id','=', $vehicle->id)->where('route_time','=', 'am')->get();
        $veh_ch_pm = DB::table('vehicle_students')->where('vehicle_id','=', $vehicle->id)->where('route_time','=', 'pm')->get();
        
        $final_array = ["am" => [], "pm" => []];

        foreach ($veh_ch_am as $vh) {
            $student = Student::where('id', '=', $vh->student_id)->first();
            $parent = User::where('id','=', $student->parent_id)->first();
            $obj = new stdClass;
            $obj->id = $student->id;
            $obj->std_fname = $student->first_name;
            $obj->std_lname = $student->last_name;
            $obj->parent_name = $parent->name ?? "";
            $obj->parent_phone = $parent->phone_num ?? "";
            array_push($final_array["am"], $obj);
        }

        foreach ($veh_ch_pm as $vh) {
            $student = Student::where('id', '=', $vh->student_id)->first();
            $parent = User::where('id','=', $student->parent_id)->first();
            $obj = new stdClass;
            $obj->id = $student->id;
            $obj->std_fname = $student->first_name;
            $obj->std_lname = $student->last_name;
            $obj->parent_name = $parent->name ?? "";
            $obj->parent_phone = $parent->phone_num ?? "";
            array_push($final_array["pm"], $obj);
        }

        return response($final_array);
    }
    /**
     * return check list
     */
    public function returnCheckList($id)
    {
        $schoolTrip = SchoolTrip::find($id);

        $returnCheckList = ReturnChecklist::where('schooltrip_id', '=', $schoolTrip->id)->get();

        $final_array = [];

        foreach ($returnCheckList as $checkList) {
            $student = Student::where('id','=', $checkList->student_id)->first();
            $parent = User::where('id','=', $student->parent_id)->first();
            $obj = new stdClass;
            $obj->id = $checkList->id;
            $obj->std_first_name = $student->first_name;
            $obj->std_last_name = $student->last_name;
            $obj->parent_name = $parent->name;
            $obj->parent_phone = $parent->phone_num;
            $obj->attendance = $checkList->attendance;

            array_push($final_array, $obj);
        }

        if(count($final_array) == 0) {
            return response([]);
        }

        return response($final_array);
    }

        /**
     * get students in trip
     */
    public function depatureCheckList($id)
    {
        $user = auth('api')->user();

        $vehicle = Vehicle::where('attendant_id','=', $user->id)->first() ?? Vehicle::where('driver_id','=', $user->id)->first();

        if (! $vehicle) {
            return abort('404','vehicle not found');
        }

        $schoolTrip = SchoolTrip::find($id);

        $depatureCheckList = DepatureChecklist::where('schooltrip_id', '=', $schoolTrip->id)->get();

        if(count($depatureCheckList) <= 0) {
            return response(null);
        }

        $final_array = [];

        foreach ($depatureCheckList as $checkList) {
            $student = Student::where('id','=', $checkList->student_id)->first();
            $parent = User::where('id','=', $student->parent_id)->first();
            $obj = new stdClass;
            $obj->id = $checkList->id;
            $obj->std_first_name = $student->first_name;
            $obj->std_last_name = $student->last_name;
            $obj->parent_name = $parent->name;
            $obj->parent_phone = $parent->phone_num;
            $obj->attendance = $checkList->attendance;

            array_push($final_array, $obj);
        }

        if (count($final_array) == 0) {
            return response([]);
        }

        
        return response($final_array);
    }

        /**
     * store depature checklist
     */
    public function storeDepatureCheckList(Request $request, $id)
    {
        $schoolTrip = SchoolTrip::find($id);

        $json = json_decode($request->getContent(), true);

        $depatureCheckList = DepatureChecklist::where('schooltrip_id', '=', $schoolTrip->id)
                                ->where('student_id','=',$json['student_id'])
                                ->first();
        
        if (! $depatureCheckList) {
            return abort(404, 'not found'); 
        }   
        DB::transaction(function () use ($request, $json, $schoolTrip, $depatureCheckList) {
            
            if ($depatureCheckList->attendance == "present") {
                $depatureCheckList->attendance = "absent";
                //store return checklist
                $returnCheckList = ReturnChecklist::where('schooltrip_id', '=', $schoolTrip->id)
                ->where('student_id','=',$json['student_id'])
                ->first();
                if ($returnCheckList) {
                    $returnCheckList->delete();
                }
            } else {
                $depatureCheckList->attendance = "present";

                $returnCheckList = ReturnChecklist::where('schooltrip_id', '=', $schoolTrip->id)
                ->where('student_id','=',$json['student_id'])
                ->first();

                if (! $returnCheckList) {
                    $createReturnCheckList = new ReturnChecklist();
                    $createReturnCheckList->schooltrip_id = $schoolTrip->id;
                    $createReturnCheckList->student_id = $json['student_id'];
                    $createReturnCheckList->attendance = 'absent';
                    $createReturnCheckList->save();
                }
                
            }
            
            $depatureCheckList->update();
            
        });
        
        return response(['msg'=>'data added']);
    }

    /**
     * store depature checklist
     */
    public function storeReturnCheckList(Request $request, $id)
    {
        $schoolTrip = SchoolTrip::find($id);

        $json = json_decode($request->getContent(), true);

        $returnCheckList = ReturnChecklist::where('schooltrip_id', '=', $schoolTrip->id)
                            ->where('student_id','=',$json['student_id'])
                            ->first();

        if ($returnCheckList->attendance == "present") {
            $returnCheckList->attendance = "absent";
        } else {
            $returnCheckList->attendance = "present";
        }

        if ($returnCheckList->update()) {
            return response(['msg'=>'data added']);
        }

        return response()->json(["message" => "not found"], 401);
    }

}
