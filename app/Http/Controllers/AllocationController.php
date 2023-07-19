<?php

namespace App\Http\Controllers;

use App\Models\FeePayment;
use App\Models\Route;
use App\Models\RoutePolyline;
use App\Models\SAndT;
use App\Models\SchoolFees;
use App\Models\SchoolTermDate;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\StudentFeeDetails;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class AllocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $student = Student::find($id);
        $term = SchoolTermDate::where('status','=',1)->first();
        $fee = SchoolFees::where('grade','=',$student->grade)->where('term','=',$term->id)->first();

        if (!$student->lat) {
            return redirect()->route('students.index')->with('unsuccess', 'Pickup location not provided');
        }

        if (!$student->lng) {
            return redirect()->route('students.index')->with('unsuccess', 'Pickup location not provided');
        }

        if (! $fee) {
            return redirect()->route('create_school_fees')->with('unsuccess','Add fee to student');
        }

       
        return view('allocation.create', compact('student'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createDropOff($id)
    {
        $student = Student::find($id);

        $term = SchoolTermDate::where('status','=',1)->first();
        $fee = SchoolFees::where('grade','=',$student->grade)->where('term','=',$term->id)->first();

        if (!$student->lat_drop) {
            return redirect()->route('students.index')->with('unsuccess', 'Drop-off location not provided');
        }

        if (!$student->lng_drop) {
            return redirect()->route('students.index')->with('unsuccess', 'Drop-off location not provided');
        }

        if (! $fee) {
            return redirect()->route('create_school_fees')->with('unsuccess','Add fee to student');
        }

        return view('allocation.dropoff', compact('student'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->trip) {
            return redirect()->back()->with('unsuccess','Kindly select trip');
        }

        if ($request->trip == 'Select Trip') {
            return redirect()->back()->with('unsuccess','Kindly select trip');
        }

        if ($request->vehicle == 'select vehicle') {
            return redirect()->back()->with('unsuccess','Kindly select vehicle');
        }

        if ($request->route == 'select route') {
            return redirect()->back()->with('unsuccess','Kindly select vehicle');
        }


        $student = Student::find($request->student);
        //check if allocated
        $status = DB::table('vehicle_students')->where('student_id','=', $student->id)->where('route_time','=','am')->get();

        //check if fee has payments
        $fee_has_payments = false;

        $student_has_fees = false;

        if ($status->isEmpty()) {
            //get the zone and add to student fee
            $zone = Zone::find($request->zone);
            $term = SchoolTermDate::where('status','=',1)->first();
            $fee = SchoolFees::where('grade','=',$student->grade)->where('term','=',$term->id)->first();
            $studentFee = StudentFee::where('term','=',$term->id)->where('student_id','=',$student->id)->first();

            if (! $studentFee) {
                return redirect()->route('students.index')->with('unsuccess','Assign school fees to student');
            }

            DB::transaction(function() use ($request, $student, $status,$zone, $term, $fee, $studentFee) {
                DB::table('vehicle_students')->insert([
                    'student_id' => $student->id,
                    'route_time' => "am",
                    "vehicle_id" => $request->vehicle
                ]);
                
                $studentFeeDetails = new StudentFeeDetails();
                $studentFeeDetails->student_fees_id = $studentFee->id;
                $studentFeeDetails->detail = 'transport';
                if (($student->lat && $student->lng) && (!$student->lat_drop && !$student->lng_drop)) {
                    $studentFeeDetails->detail_amount = $zone->oneway_price;
                    $studentFee->amount += $zone->oneway_price;
                } else {
                    $studentFeeDetails->detail_amount = $zone->price;
                    $studentFee->amount += $zone->price;
                }
                $studentFee->update();
                $studentFeeDetails->save();

                if ($student->trip_type == 1) {
                    $student->bus_assigned = 1;
                    $student->update();
                }
                
        
                $stdtrip = new SAndT();
                $stdtrip->student_id = $student->id;
                $stdtrip->trip_id = $request->trip;
                $stdtrip->save();

            });

            $dropOffOnly = false;

            if (($student->lat && $student->lng) && (!$student->lat_drop && !$student->lng_drop)) {
                $dropOffOnly = true;
            }

            if ($dropOffOnly) {
                return redirect()->route('students.index')->with('success','Pickup allocation was successfull');
            }

            return redirect()->route('allocation_create_dropoff', $student->id)->with('success','Pickup allocation was successfull');
        } else {
            //get the zone and add to student fee
            $zone = Zone::find($request->zone);
            $term = SchoolTermDate::where('status','=',1)->first();
            $fee = SchoolFees::where('grade','=',$student->grade)->where('term','=',$term->id)->first();
            $studentFee = StudentFee::where('term','=',$term->id)->where('student_id','=',$student->id)->first();
            //check if fee has payments
           
            if (! $studentFee) {
                return redirect()->route('students.index')->with('unsuccess','Assign school fees to student');
            }
            $check = FeePayment::where('school_fees_id','=',$studentFee->id)->get();

            


            DB::transaction(function() use ($request, $student, $status, $zone, $check, $studentFee) {
                if ($check->isEmpty()) {
                    $studentFeeDetailsCheck = StudentFeeDetails::where('student_fees_id','=',$studentFee->id)->where('detail','=','transport')->first();
                    if ($studentFeeDetailsCheck) {
                        $studentFeeDetailsCheck->delete();
                    }
                    $studentFeeDetails = new StudentFeeDetails();
                    $studentFeeDetails->student_fees_id = $studentFee->id;
                    $studentFeeDetails->detail = 'transport';
                    if (($student->lat && $student->lng) && (!$student->lat_drop && !$student->lng_drop)) {
                        $studentFeeDetails->detail_amount = $zone->oneway_price;

                    } else {
                        $studentFeeDetails->detail_amount = $zone->price;

                    }
                    $studentFeeDetails->save();

                    $amt = 0;
                    $loop_fee_details = StudentFeeDetails::where('student_fees_id','=',$studentFee->id)->get();

                    foreach ($loop_fee_details as $key => $dt) {
                        $amt += $dt->detail_amount;
                    }
                    $studentFee->amount = $amt;
                    $studentFee->update();

                    foreach ($status as $item) {
                        DB::table('vehicle_students')->where('id','=', $item->id)->delete();
                    }
    
                    DB::table('vehicle_students')->insert([
                        'student_id' => $student->id,
                        'route_time' => "am",
                        "vehicle_id" => $request->vehicle
                    ]);

                    $sandt = SAndT::where('student_id', '=', $student->id)->get();

                    foreach ($sandt as $s) {
                        $s->delete();
                    }
                    DB::table('s_and_t_s')->where('student_id','=',$student->id)->delete();
            
                    $stdtrip = new SAndT();
                    $stdtrip->student_id = $student->id;
                    $stdtrip->trip_id = $request->trip;
                    $stdtrip->save();
    
                } 
            });
            

            if ($check->isNotEmpty()) {
                $fee_has_payments = true;
            }

            $dropOffOnly = false;

            if (($student->lat && $student->lng) && (!$student->lat_drop && !$student->lng_drop)) {
                $dropOffOnly = true;
            }

            if ($dropOffOnly) {
                return redirect()->route('students.index')->with('success','Pickup allocation was successfull');
            }

            if ($fee_has_payments) {
                return redirect()->route('students.index')->with('unsuccess','Cannot allocate another bus because of transport payment');
            }

            return redirect()->route('allocation_create_dropoff', $student->id)->with('success','Pickup allocation was successfull');
        }
        
    }

        /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeDropOff(Request $request)
    {
        $student = Student::find($request->student);

        if (!$request->trip) {
            return redirect()->back()->with('unsuccess','Kindly select trip');
        }

        if ($request->trip == 'Select Trip') {
            return redirect()->back()->with('unsuccess','Kindly select trip');
        }

        if ($request->vehicle == 'select vehicle') {
            return redirect()->back()->with('unsuccess','Kindly select vehicle');
        }

        if ($request->route == 'select route') {
            return redirect()->back()->with('unsuccess','Kindly select vehicle');
        }

        $status = DB::table('vehicle_students')->where('student_id','=', $student->id)->where('route_time','=','pm')->get();

        $student->bus_assigned = 1;
        $student->update();

        $zone = Zone::find($request->zone_id);
        $term = SchoolTermDate::where('status','=',1)->first();
        $fee = SchoolFees::where('grade','=',$student->grade)->where('term','=',$term->id)->first();
        $studentFee = StudentFee::where('term','=',$term->id)->where('student_id','=',$student->id)->first();

        if (! $studentFee) {
            return redirect()->route('students.index')->with('unsuccess','Assign school fees to student');
        }
        
        $fee_has_payments = false;


        if ($status->isEmpty()) {
            DB::table('vehicle_students')->insert([
                'student_id' => $student->id,
                'route_time' => "pm",
                "vehicle_id" => $request->vehicle
            ]);

            
            if ((!$student->lat && !$student->lng) && ($student->lat_drop && $student->lng_drop)) {
                $studentFeeDetails = new StudentFeeDetails();
                $studentFeeDetails->student_fees_id = $studentFee->id;
                $studentFeeDetails->detail = 'Transport';
                $studentFeeDetails->detail_amount = $zone->oneway_price;
                $studentFeeDetails->save();
                $studentFee->amount += $zone->oneway_price;
                $studentFee->update();
            }

            $stdtrip = new SAndT();
            $stdtrip->student_id = $student->id;
            $stdtrip->trip_id = $request->trip;
            $stdtrip->save();
        } else {
            $check = FeePayment::where('school_fees_id','=',$studentFee->id)->get();

            if (! $studentFee) {
                return redirect()->route('students.index')->with('unsuccess','Assign school fees to student');
            }

            DB::transaction(function() use ($request, $student, $status, $studentFee, $zone, $check) {
                //check if fee has payments

                if ($check->isEmpty()) {
                    foreach ($status as $item) {
                        DB::table('vehicle_students')->where('id','=', $item->id)->delete();
                    }
    
                    DB::table('vehicle_students')->insert([
                        'student_id' => $student->id,
                        'route_time' => "pm",
                        "vehicle_id" => $request->vehicle
                    ]);
    
                    if ((!$student->lat && !$student->lng) && ($student->lat_drop && $student->lng_drop)) {
                        $studentFeeDetailsCheck = StudentFeeDetails::where('student_fees_id','=',$studentFee->id)->where('detail','=','transport')->first();
                        if ($studentFeeDetailsCheck) {
                            $studentFeeDetailsCheck->delete();
                        }
                        $studentFeeDetails = new StudentFeeDetails();
                        $studentFeeDetails->student_fees_id = $studentFee->id;
                        $studentFeeDetails->detail = 'transport';
                        $studentFeeDetails->detail_amount = $zone->oneway_price;
                        $studentFeeDetails->save();
                        DB::table('s_and_t_s')->where('student_id','=',$student->id)->delete();
    
                        $amt = 0;
    
                        $loop_fee_details = StudentFeeDetails::where('student_fees_id','=',$studentFee->id)->get();
    
                        foreach ($loop_fee_details as $key => $dt) {
                            $amt += $dt->detail_amount;
                        }
                        $studentFee->amount = $amt;
                        $studentFee->update();

                        DB::table('s_and_t_s')->where('student_id','=',$student->id)->delete();
                    }


                    $stdtrip = new SAndT();
                    $stdtrip->student_id = $student->id;
                    $stdtrip->trip_id = $request->trip;
                    $stdtrip->save();
                }

                
            });

            if ($check->isNotEmpty()) {
                return redirect()->route('students.index')->with('unsuccess','Cannot re allocate student because fee has payment');
            }
        }

        return redirect()->route('students.index')->with('success','Allocation was successfull');
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

    /**
     * get zones
     */
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

    public function getZoneRoutes($id)
    {
        $zone = Zone::find($id);

        $route_zone = DB::table('route_zones')->where('zone_id','=', $zone->id)->get();

        $final_array = [];

        foreach ($route_zone as $rz) {
            $route = Route::where('id','=', $rz->route_id)->first();
            $routePolyLine = RoutePolyline::where('route_id','=',$route->id)->first();
            $inner_array = ["route" => "", "polyline" => ""];
            $inner_array["route"] = $route;
            $inner_array["polyline"] = $routePolyLine;
            array_push($final_array, $inner_array);
        }

        return response($final_array);
    }

    /**
     * when select for student changes get the details for parent
     */
    public function getStudent($id)
    {
        $student = Student::find($id);

        $parent = User::where('id','=', $student->parent_id)->first();

        return response(["student" => $student, 'parent' => $parent]);
    }

    /**
     * gets the vehicle for selected route
     */
    public function getVehicle(Request $request)
    {
        $student = Student::find($request->student_id);

        $trip_grade = DB::table('vehicle_routes')->where('route_id','=', $request->route_id)->get();

        $final_array = [];

        foreach ($trip_grade as $tg) {
            $vehicle = Vehicle::where('id','=', $tg->vehicle_id)->first();

            if ($vehicle) {
                array_push($final_array, $vehicle);
            }
        }

        return response($final_array);
    }

    public function getVehicleTrip (Request $request) 
    {
        $vehicle = Vehicle::find($request->vehicle);
        $time = $request->time;
        $trips = Trip::where('vehicle_id','=', $vehicle->id)->where('route_id','=', $request->route)->where('time','=', $time)->get();
        $tripsTwo = new Collection();
        foreach ($trips as $key => $trip) {
            $gr_tr =  DB::table('grade_groups')->where('trip_id','=', $trip->id)->where('grade_id','=', $request->grade)->first();
            $grade_name = DB::table('student_classes')->where('id','=',$request->grade)->first();
            $grade_name_name = '';
            if ($grade_name) {
                $grade_name_name = $grade_name->name;
            }
            if ($gr_tr) {
                $tripsTwo->push($trip);
            }
        }

        if (count($tripsTwo) <= 0) {
            $myselect = "<p class='text-danger mt-4'>No trips for student </p>";
        } else {
            $myselect = "<label class='form-label' id='myedittrips'>Select Trips</label><select name='trip' class='form-select' data-width='100%' id='tripsss' required><option>Select Trip</option>";
            foreach ($tripsTwo as $trips) {
                $myselect .= "<option value='$trips->id'>Title: $trips->title, AM/PM: $trips->time,  From: $trips->time_from,  To: $trips->time_to</option>";
            }
            $myselect .= '</select>';
        }

        return response($myselect);
    }

    /**
     * check if student has been assigned am
     */
    public function chckIfAllocatedAm($id)
    {
        $student = Student::find($id);

        $status = DB::table('vehicle_students')->where('student_id','=', $student->id)->where('route_time','=','am')->first();

        if ($status) {
            return response(1);
        } else {
            return response(0);
        }
    }
}
