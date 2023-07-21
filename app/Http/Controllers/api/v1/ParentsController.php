<?php

namespace App\Http\Controllers\api\v1;

use App\Events\NewMessageNotification;
use App\Events\NewNotification;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\DepatureChecklist;
use App\Models\FeePayment;
use App\Models\FlagOff;
use App\Models\Invoice;
use App\Models\Review;
use App\Models\SAndT;
use App\Models\SchoolTermDate;
use App\Models\SchoolTrip;
use App\Models\SchoolTripGrade;
use App\Models\Settings;
use App\Models\StandinBus;
use App\Models\StandinDriver;
use App\Models\Stream;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\StudentFeeDetails;
use App\Models\TermEvent;
use App\Models\TermHoliday;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\GeneratedPassword;
use App\Notifications\ShareAppLinkNotification;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use stdClass;

class ParentsController extends Controller
{
    public function parentId()
    {
        $user = auth('api')->user();

        return response(['user' => $user->id]);
    }

    public function myChildren($id)
    {
        $user = User::find($id) ?? abort(404, 'not found');
        $children = Student::where('parent_id', '=', $user->id)->where('transport','=', 1)->get();

        if($user->user_type == 'parent two') {
            $children = Student::where('parent_id', '=', $user->linked_to)->where('transport','=', 1)->get();
        }
        


        $schoolterm = SchoolTermDate::where('status', '=', 1)->first();

        $final_arary = [];

        foreach ($children as $child) {
            $student_fee = StudentFee::where('student_id','=',$child->id)->where('term','=', $schoolterm->id)->first();
            $feePayments = FeePayment::where('school_fees_id','=',$student_fee->id)->get();
            $amt_paid = 0;
            foreach ($feePayments as $key => $det) {
                $amt_paid += $det->amount_paid;
            }
            $balance = $student_fee->amount - $amt_paid;
            $grade = DB::table('student_classes')->where('id','=', $child->grade)->first();

            $child_array = [
                'id' => $child->id,
                'vehicle_id' => $child->vehicle_id ?? '',
                'parent_id' => $child->parent_id ?? '',
                'first_name' => $child->first_name ?? '',
                'last_name' => $child->last_name ?? '',
                'grade' => $grade->name ?? '',
                'gender' => $child->gender,
                "admission_num" => $child->add_num,
                'stream' => Stream::where('id','=', $child->stream)->first()->name ?? '',
                'lat' => $child->lat ?? '',
                'lng' => $child->lng ?? '',
                'lat_drop' => $child->lat_drop ?? '',
                'lng_drop' => $child->lng_drop ?? '',
                'pick_up' => $child->pick_up ?? '',
                'confirm_pickup_parent' => $child->confirm_pickup_parent ?? '',
                'confirm_pickup_driver' => $child->confirm_pickup_driver ?? '',
                'pickup_changed' => $child->pickup_changed ?? '',
                'parent_two' => $child->parent_two ?? '',
                'other' => $child->other ?? '',
                'image' => asset('store/'.$child->image) ?? NULL,
                'transport_type' => $child->trip_type,
                'fee_balance' => $balance
            ];

            array_push($final_arary, $child_array);
        }

       

        return response($final_arary);
    }

    public function myChild($id)
    {
        $student = Student::find($id);
        if ($student->transport == 1) {
            $grade = DB::table('student_classes')->where('id','=', $student->grade)->first();

            $obj = new stdClass;
            $obj->id =  $student->id;
            $obj->vehicle_id =  $student->vehicle_id;
            $obj->parent_id =  $student->parent_id;
            $obj->first_name =  $student->first_name;
            $obj->last_name =  $student->last_name;
            $obj->grade =  $grade->name;
            $obj->stream = Stream::where('id','=', $student->stream)->first()->name ?? '';
            $obj->gender = $student->gender;
            $obj->admission_num =  $student->add_num;
            $obj->lat =  $student->lat;
            $obj->lng =  $student->lng;
            $obj->lat_drop =  $student->lat_drop;
            $obj->lng_drop =  $student->lng_drop;
            $obj->pick_up =  $student->pick_up;
            $obj->confirm_pickup_parent =  $student->confirm_pickup_parent;
            $obj->confirm_pickup_driver =  $student->confirm_pickup_driver;
            $obj->pickup_changed =  $student->pickup_changed;
            $obj->parent_two =  $student->parent_two;
            $obj->other =  $student->other;
            $obj->image =  asset('store/'.$student->image);
            $obj->transport_type = $student->trip_type;
    
            return response([$obj]);
        }

        return response([]);
    }


    public function testNotif()
    {
        $parent = auth('api')->user();

        event(new NewMessageNotification($parent->id, 'Test notification', 'This is a test notification'));

	    return response('notification sent');
    }

    /**
     * @param user
     */
    public function addUsers(Request $request, $id)
    {
        $user = User::find($id);

        $children = Student::where('parent_id','=', $user->id)->get();

        $request->validate([
            'user_type' => 'required', //parent two or other
            'id_num' => 'required',
            'phone_num' => 'required',
            'email' => 'required|email',
        ]);

        $generator = new ComputerPasswordGenerator();
        $generator->setLowercase()->setNumbers(false)->setSymbols(false)->setLength(6);
        $password = $generator->generatePassword();


        DB::transaction(function() use ($request, $password, $children, $user) {


            if ($request->relationship == 'father' || $request->relationship == 'mother' || $request->relationship == 'guardian') {
                $check = User::where('user_type','=', 'parent two')->where('linked_to','=', $user->id)->first();
                if ($check) {
                    $check->delete();
                }
                $parentTwo = new User();
                $parentTwo->name = $request->first_name . ' ' . $request->second_name . ' ' .  $request->last_name;
                $parentTwo->email = $request->email;
                $parentTwo->id_num = $request->id_num;
                $parentTwo->phone_num = $request->phone_num;
                $parentTwo->user_type = 'parent two';
                $parentTwo->password_changed = 0;
                $parentTwo->password = Hash::make($password);
                $parentTwo->linked_to = $user->id;

                $parentTwo->save();

                Notification::send($parentTwo, new GeneratedPassword($password));
                
                Notification::send($user, new ShareAppLinkNotification($password));

                foreach ($children as $child) {
                    $child->parent_two = $parentTwo->id;
                    $child->update();
                }
            }

            if ($request->user_type == 'house manager') {
                $checkOther = User::where('user_type','=','other')->where('linked_to','=',$user->id)->first();
                if ($checkOther) {
                    $checkOther->delete();
                }
                $parentTwo = new User();
                $parentTwo->name = $request->first_name . ' ' . $request->second_name . ' ' .  $request->last_name;
                $parentTwo->email = $request->email;
                $parentTwo->id_num = $request->id_num;
                $parentTwo->phone_num = $request->phone_num;
                $parentTwo->user_type = 'other';
                $parentTwo->password_changed = 0;
                $parentTwo->password = Hash::make($password);
                $parentTwo->linked_to = $user->id;
                $parentTwo->save();

                foreach ($children as $child) {
                    $child->other = $parentTwo->id;
                    $child->update();
                }

                Notification::send($parentTwo, new GeneratedPassword($password));
            }
        });

        if($request->user_type == 'parent two') {
            return response('Check mail for password');
        }

        if($request->user_type == 'other') {
            return response('');
        }
    }


    public function vehiclechildCoord($id)
    {
        $time = date('a');

        $student = Student::find($id);

        $vehicleStudent = DB::table('vehicle_students')->where('student_id','=', $student->id)
                            ->where('route_time','=', $time)
                            ->first();

        $vehicle = Vehicle::find($vehicleStudent->vehicle_id);


        $check_stand_in = StandinBus::where('original_vehicle','=', $vehicle->id)->where('status','=', 1)->first();
        if ($check_stand_in) {
            $vehicle = Vehicle::find($check_stand_in->stand_in_vehicle);
        }


        $trip = Trip::where('vehicle_id','=', $vehicle->id)->where('time','=', $time)->first() ?? null;

        $vehicleCordinates = ['vehicle_id'  => '','time' => $time,'lat' => '', 'lng' => '', 'title' => '', 'plate' => '', 'head' => '', 'speed' => ''];
        $studentHomeCordinates = ['std_name' => '','lat' => '', 'lng' => ''];

        $vehicleCordinates['vehicle_id'] = $vehicle->id;
        $vehicleCordinates['lat'] = $vehicle->latitude;
        $vehicleCordinates['lng'] = $vehicle->longitude;
        $vehicleCordinates['title'] = $vehicle->title;
        $vehicleCordinates['plate'] = $vehicle->plate_num;
        $vehicleCordinates['head'] = $vehicle->head;
        $vehicleCordinates['speed'] = $vehicle->speed;

        $studentHomeCordinates['std_name'] = $student->first_name . ' ' . $student->last_name;

        if ($time == "am") {
            $studentHomeCordinates['lat'] = $student->lat;
            $studentHomeCordinates['lng'] = $student->lng;
        } 

        if ($time == "pm") {
            $studentHomeCordinates['lat'] = $student->lat_drop;
            $studentHomeCordinates['lng'] = $student->lng_drop;
        } 

        $settings = Settings::find(1);
        $obj = new stdClass;
        $obj->lat = $settings->lat;
        $obj->lng = $settings->lng;

        return response(['vehicle_coordinates' => $vehicleCordinates, 'student_home_coordinates' => $studentHomeCordinates, 'trip' => $trip, "school_location" => $obj]);
    }
    /***
     * flag off student for the day
     */
    public function flagOff(Request $request, $id)
    {
        $date = date('Y-m-d');

        $json = json_decode($request->getContent(), true);
        
        $flag = new FlagOff();
        $flag->parent_id = auth('api')->user()->id;
        $flag->student_id = $id;
        $flag->reason = $json['reason'];
        $flag->time = $json['time'];
        $flag->date = $date;

        if ($flag->save()) {
            return response('student will not be picked up');
        }

        return response('System error please try again');
    }
    /**
     * 
     * attendance work
     */
    public function getAttendanceData($id)
    {
        $parent = auth('api')->user();
        $student = Student::find($id);

        /*

        $attendance = ['name'=> '', 'grade' => '','NumOfDaysAbsent' => 0, 'dates' => []];

        $finalArr = ["attendance" => "", "flagoffs" => ""];

        foreach ($absentDays as $key => $absentDay) {
            $attendance['dates'][$key] = $absentDay->date; 
        }

        $attendance['name'] = $student->firstname . ' ' . $student->last_name;
        $attendance['grade'] = $student->grade;
        $attendance['NumOfDaysAbsent'] = count($absentDays) + count($flagoff);

        $finalArr["attendance"] = $attendance;
        $finalArr["flagoffs"] = $flagoff;
        */
        $absentDays = Attendance::where('present', '=', 'false')->where('student_id', '=', $student->id)->get();
        $flagoff = FlagOff::where('student_id','=', $student->id)->get();
        $attendance_data = new Collection();

        foreach ($flagoff as $key => $flag) {
            $obj = new stdClass;
            $obj->student_id = $student->id;
            $obj->parent_id = $parent->id;
            $obj->reason = $flag->reason;
            $obj->trip = $flag->time;
            $obj->date = $flag->date;

            $attendance_data->push($obj);
        }

        foreach ($absentDays as $key => $absentDay) {
            $obj = new stdClass;
            $obj->student_id = $student->id;
            $obj->parent_id = $parent->id;
            $obj->reason = 'Visit office';
            $obj->trip = 'full day';
            $obj->date = $absentDay->date;

            $attendance_data->push($obj);
        }

        return response($attendance_data);
    }
    /**
     * 
     * Term Trips
     * 
    */
    public function schoolTrips($id)
    {
        $student = Student::find($id);

        if(! $student) {
            return abort(404, 'not found');
        }

        $schoolterm = SchoolTermDate::where('status', '=', 1)->first();

        $studentTripDetails = ['id' => '', 'name' => '', 'trips' => null];
        //paid trip and grade
        $tripWithGrade = SchoolTripGrade::where('grade_id','=', $student->grade)->get();

        $schooltips_final = [];

        foreach ($tripWithGrade as $key => $trip) {
            $check_trip_grades = SchoolTrip::where('term_id', '=', $schoolterm->id)->where('id','=',$trip->schooltrip_id)->first();
           
            if ($check_trip_grades) {
                $teachers = DB::table('schooltrip_teacher')->where('schooltrip_id','=', $check_trip_grades->id)->get();

                $teacher_final = [];
    
                foreach ($teachers as $teacher) {
                    $tech = User::where('id','=', $teacher->teacher_id)->first();
                    $objTech = new stdClass;
                    $objTech->teacher_name = $tech->name;
                    $objTech->teacher_phone = $tech->phone_num;
                    array_push($teacher_final, $objTech);
                }
    
                $vehicle_final = [];
    
                $vehicles = DB::table('schooltrip_vehicle')->where('schooltrip_id','=', $check_trip_grades->id)->get();
                foreach ($vehicles as $key => $vehicle) {
                    $veh = Vehicle::where('id','=', $vehicle->vehicle_id)->first();
                    $objVeh = new stdClass;
                    $objVeh->vehicle_id = $veh->id;
                    $objVeh->vehicle_title = $veh->title;
                    $objVeh->vehicle_plate = $veh->plate_num;
                    $objVeh->driver_id = $veh->driver->id;
                    $objVeh->driver_name = $veh->driver->name;
                    $objVeh->driver_number = $veh->driver->phone_num;
                    array_push($vehicle_final, $objVeh);
                }
                    
                
                if ($check_trip_grades->has_more_destinations) {
                    $destinationsT = DB::table('school_trips_destinations')->where('school_trip_id','=', $check_trip_grades->id)->get()->pluck('destination');
                } else {
                    $destinationsT = DB::table('school_trips_destinations')->where('school_trip_id','=', $check_trip_grades->id)->get()->pluck('destination');
                }


                return response($trip);

                if (!$check_trip_grades->has_more_destinations) {
                    $origin = '('.$check_trip_grades->trip_route .')';
                    $dest = $check_trip_grades->dest_app;
                } else {
                    $origin = $check_trip_grades->trip_route;
                    $dest = $check_trip_grades->destination;
                }
    
    
                $inner_trips_with_grade = [
                    'id' => $check_trip_grades->id,
                    'trip_name' => $check_trip_grades->trip_name,
                    'destination_name' => $destinationsT,
                    'teachers' => $teacher_final,
                    'vehicle' => $vehicle_final,
                    'trip_origin_lat_lng' => $origin,
                    'trip_destination_lat_lng' => $dest,
                    'status' => $check_trip_grades->status,
                    'grade' => $check_trip_grades->grade,
                    'price' => $check_trip_grades->price,
                    'trip_date' => $check_trip_grades->trip_date,
                    'departure_time' => $check_trip_grades->departure_time,
                    'return_time' => $check_trip_grades->return_time,
                    'has_more_destinations' => $check_trip_grades->has_more_destinations,
                    'way_point_one' => $check_trip_grades->waypont_one,
                    'way_point_two' => $check_trip_grades->waypont_two,
                    'way_point_three' => $check_trip_grades->waypont_three,
                    'way_point_four' => $check_trip_grades->waypont_four,
                    'way_point_five' => $check_trip_grades->waypont_five,
                    'way_point_six' => $check_trip_grades->waypont_six,
                    'way_point_seven' => $check_trip_grades->waypont_seven,
                    'way_point_eight' => $check_trip_grades->waypont_eight,
                ];

                array_push($schooltips_final, $inner_trips_with_grade);

            }
        }
        
        //add students details to students trips
        $studentTripDetails['name'] = $student->first_name . ' ' . $student->last_name;
        $studentTripDetails['id'] = $student->id;
        $studentTripDetails['trip'] = $schooltips_final;

        return response($studentTripDetails);
    }

    /**
     * @param Teacher $id
     * @return Json
     * get teacher
     */
    public function teacher($id)
    {
        $student = Student::find($id);

        $stream = Stream::where('id','=', $student->stream)->first();

        $teacher = User::where('id','=',$stream->class_teacher)->where('status', '=', 1)->first();

        return response($teacher);
    }
    /**
     * @return schoolterms
     */
    public function schoolTerm()
    {
        $schoolterm = SchoolTermDate::where('status', '=', 1)->first();

        return response($schoolterm);
    }
    /**
     * @return termholidays
     */
    public function holidays()
    {
        $schoolterm = SchoolTermDate::where('status', '=', 1)->first();

        $holidays = TermHoliday::where('term_id', '=', $schoolterm->id)->orderBy('created_at','desc')->get();

        return response($holidays);
    }

    /**
     * @return termevents
     */
    public function termEvents()
    {
        $schoolterm = SchoolTermDate::where('status', '=', 1)->first();

        $events = TermEvent::where('term_id', '=', $schoolterm->id)->get();

        return response($events);
    }

    /**
     * @param Driver
     * @return array driver attendant vehicle
     * driver information
    */
    public function driverInfo($id)
    {
        $student = Student::find($id);

        $studentTrips = SAndT::where('student_id','=', $student->id)->get();

        $final_array = [];

        foreach ($studentTrips as $key => $studentTrip) {
            $trip = Trip::where('id','=', $studentTrip->trip_id)->first();

            $vehicle = Vehicle::where('id','=', $trip->vehicle_id)->first();

            $attendant = User::find($vehicle->attendant_id);
            $attendant_obj = new stdClass;
            $attendant_obj->name = $attendant->name;
            $attendant_obj->email = $attendant->email;
            $attendant_obj->phone_num = $attendant->phone_num;
            $attendant_obj->image = asset('store/'.$attendant->image);


            $check_stand_in = StandinBus::where('original_vehicle','=', $vehicle->id)->where('status','=', 1)->first();
            if ($check_stand_in) {
                $vehicle = Vehicle::find($check_stand_in->stand_in_vehicle);
            }
            
            $driver = User::find($vehicle->driver_id);
            $driver_obj = new stdClass;
            $driver_obj->name = $driver->name;
            $driver_obj->email = $driver->email;
            $driver_obj->phone_num = $driver->phone_num;
            $driver_obj->image = asset('store/'.$driver->image);

           

            $inner_array = ["trip" => $trip, "driver" => $driver_obj, "attendant" => $attendant_obj];

            array_push($final_array, $inner_array);
        }

        return response($final_array);
    }
    /**
     * @param Parent
     * @return UnpaidInvoice
     * paid invoices
     */
    public function paidInv($id)
    {
        $parent = User::find($id);

        $paidInvoice = Invoice::where('parent_id', '=', $parent->id)
                                ->where('status', 'LIKE','paid')
                                ->get();

        return response($paidInvoice);
    }

    /**
     * @param Parent
     * @return UnpaidInvioce
     * unpaid invoices
     */
    public function unPaidInv($id)
    {
        $parent = User::find($id);

        $unPaidInvoice = Invoice::where('parent_id', '=', $parent->id)
                                ->where('status', 'LIKE','unpaid')
                                ->get();
                                
        return response($unPaidInvoice);
    }

    /**
     * @param Parent 
     * @return string
     * all notifications 
     */
    public function allNotifs($id)
    {
        $parent = User::find($id);

        $notifications = $parent->notifications;
                                
        return response($notifications);
    }
    /**
     * mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = DatabaseNotification::find($id);

        $notification->markAsRead();

        return response('marked');
    }

    /**
     * store pickup point
     */
    public function storePickup($id, Request $request)
    {
        $json = json_decode($request->getContent(), true);


        $student = Student::find($id);

        $student->lat = $json["lat"];
        $student->lng = $json["lng"];
        $student->pickup_changed = 1;
        $student->confirm_pickup_parent = 1;

        if ($student->update()) {
            return response('Pick up saved');
        }

        return ('System error please try again');
    }

    /**
     * store pickup point
     */
    public function storeDropOff($id, Request $request)
    {
        $json = json_decode($request->getContent(), true);

        $student = Student::find($id);

        $student->lat_drop = $json['lat'];
        $student->lng_drop = $json['lng'];

        if ($student->update()) {
            return response('Drop off saved');
        }

        return ('System error please try again');
    }

    /**
     * @param Student $id
     * update student photo
     */
    public function updateStudentPhoto(Request $request, $id)
    {
        $student = Student::find($id);

        if ($student->image) {
            Storage::disk('public_uploads')->delete($student->image);
        }

        $json = json_decode($request->getContent(), true);

        // toa functionality ya 
        $file = explode(',',$json['image'], 2);

        $file = base64_decode($file[1]);
        
        $imageName = Str::random(60).'.'.'png';

        Storage::disk('public_uploads')->put('students/'.$imageName, $file);

        $student->image = 'students/'.$imageName;

        if($student->update()) {
            return response('image changed');
        }

        return abort(401);
    }

    public function getTrips($id)
    {
        $student = Student::find($id);

        $user = auth('api')->user();

        $final_arary = ["am bus" => "", "pm bus" => ""];
        //am stuff
        $vehicleAm = DB::table('vehicle_students')->where('student_id','=',$student->id)->where('route_time','=', 'am')->first();
        if($vehicleAm) {

            $vehAm = Vehicle::where('id','=',$vehicleAm->vehicle_id)->first();
            if ($vehicleAm) {
                $attendantAm = User::where('id','=',$vehAm->attendant_id)->first();
                $attendantAm->is_stand_in = false;
                $attendantAm->stand_in_from = null;
                $attendantAm->stand_in_to = null;
                $check_stand_in_attendant = StandinDriver::where('stand_in_vehicle','=', $vehAm->id)->where('status','=', 1)->first();
                if ($check_stand_in_attendant) {
                    $attendantAm = User::where('id', '=', $check_stand_in_attendant->stand_in_attendant)->first();
                    $attendantAm->is_stand_in = true;
                    $attendantAm->stand_in_from = $check_stand_in_attendant->date_from;
                    $attendantAm->stand_in_to = $check_stand_in_attendant->date_to;
                }
        
                $checkAttendantAm = $attendantAm->image ?? null;
                if ($checkAttendantAm) {
                    $attendantAm->image = asset('store/'.$attendantAm->image);
                } else {
                    if ($attendantAm->gender == 'male') {
                        $attendantAm->image = 'https://cdn-icons-png.flaticon.com/512/9875/9875255.png';
                    } else {
                        $attendantAm->image = 'https://cdn-icons-png.flaticon.com/512/9875/9875392.png';
                    }
                }
                $vehAm->is_stand_in = false;
                $vehAm->stand_in_from = null;
                $vehAm->stand_in_to = null;
                $check_stand_in = StandinBus::where('original_vehicle','=', $vehAm->id)->where('status','=', 1)->first();
                if ($check_stand_in) {
                    $vehAm = Vehicle::find($check_stand_in->stand_in_vehicle);
                    $vehAm->is_stand_in = true;
                    $vehAm->stand_in_from = $check_stand_in->date_from;
                    $vehAm->stand_in_to = $check_stand_in->date_to;
                }

                

                $driverAm = User::where('id','=',$vehAm->driver_id)->first();
                $driverAm->is_stand_in = false;
                $driverAm->stand_in_from = null;
                $driverAm->stand_in_to = null;
                $check_stand_in_driver = StandinDriver::where('stand_in_driver','=', $driverAm->id)->where('status','=', 1)->first();
                if ($check_stand_in_driver) {
                    $driverAm = User::where('id', '=', $check_stand_in_attendant->stand_in_driver)->first();
                    $driverAm->is_stand_in = true;
                    $driverAm->stand_in_from = $check_stand_in_driver->date_from;
                    $driverAm->stand_in_to = $check_stand_in_driver->date_to;
                }
                $checkDriverAm = $driverAm->image ?? null;
                if ($checkDriverAm) {
                    $driverAm->image = asset('store/'.$driverAm->image);
                } else {
                    if ($driverAm->gender == 'male') {
                        $driverAm->image = 'https://cdn-icons-png.flaticon.com/512/9875/9875255.png';
                    } else {
                        $driverAm->image = 'https://cdn-icons-png.flaticon.com/512/9875/9875392.png';
                    }
                }
                
                $tripAm = Trip::where('vehicle_id','=', $vehAm->id)->where('time','=', 'am')->first();
                $final_arary["am bus"] = ["vehicle" => $vehAm,"driver" => $driverAm,"attendant" => $attendantAm, "trip" => $tripAm];
            }
        }
        

        //pm staff
        $vehiclePm = DB::table('vehicle_students')->where('student_id','=',$student->id)->where('route_time','=', 'pm')->first();
        if ($vehiclePm) {
            $vehPm = Vehicle::where('id','=',$vehiclePm->vehicle_id)->first();
        
            if ($vehiclePm) {
    
                $attendantPm = User::where('id','=',$vehPm->attendant_id)->first();
                $attendantPm->is_stand_in = false;
                $attendantPm->stand_in_from = null;
                $attendantPm->stand_in_to = null;
                $check_stand_in_attendant_pm = StandinDriver::where('stand_in_vehicle','=', $vehPm->id)->where('status','=', 1)->first();
                if ($check_stand_in_attendant_pm) {
                    $attendantPm = User::where('id', '=', $check_stand_in_attendant_pm->stand_in_attendant)->first();
                    $attendantPm->is_stand_in = true;
                    $attendantPm->stand_in_from = $check_stand_in_attendant_pm->date_from;
                    $attendantPm->stand_in_to = $check_stand_in_attendant_pm->date_to;
                }
                $checkAttendantPm = $attendantPm->image ?? null;
                if ($checkAttendantPm) {
                    $attendantPm->image = asset('store/'.$attendantPm->image);
                } else {
                    if ($attendantPm->gender == 'male') {
                        $attendantPm->image = 'https://cdn-icons-png.flaticon.com/512/9875/9875255.png';
                    } else {
                        $attendantPm->image = 'https://cdn-icons-png.flaticon.com/512/9875/9875392.png';
                    }
                }
    
                $vehPm->is_stand_in = false;
                $vehPm->stand_in_from = null;
                $vehPm->stand_in_to = null;
                $check_stand_in_pm = StandinBus::where('original_vehicle','=', $vehPm->id)->where('status','=', 1)->first();
                if ($check_stand_in_pm) {
                    $vehPm = Vehicle::find($check_stand_in_pm->stand_in_vehicle);
                    $vehPm->is_stand_in = true;
                    $vehPm->stand_in_from = $check_stand_in_pm->date_from;
                    $vehPm->stand_in_to = $check_stand_in_pm->date_to;
                }
    
                $driverPm = User::where('id','=',$vehPm->driver_id)->first();
                $driverPm->is_stand_in = false;
                $driverPm->stand_in_from = null;
                $driverPm->stand_in_to = null;
                $check_stand_in_driver_pm = StandinDriver::where('stand_in_driver','=', $driverPm->id)->where('status','=', 1)->first();
                if ($check_stand_in_driver_pm) {
                    $driverPm = User::where('id', '=', $check_stand_in_attendant->stand_in_driver)->first();
                    $driverPm->is_stand_in = true;
                    $driverPm->stand_in_from = $check_stand_in_driver_pm->date_from;
                    $driverPm->stand_in_to = $check_stand_in_driver_pm->date_to;
                }
                $checkDriverPm = $driverPm->image ?? null;
                if ($checkDriverPm) {
                    $driverPm->image = asset('store/'.$driverPm->image);
                }else {
                    if ($driverPm->gender == 'male') {
                        $driverPm->image = 'https://cdn-icons-png.flaticon.com/512/9875/9875255.png';
                    } else {
                        $driverPm->image = 'https://cdn-icons-png.flaticon.com/512/9875/9875392.png';
                    }
                }
    
                $tripPm = Trip::where('vehicle_id','=', $vehPm->id)->where('time','=', 'pm')->first();
    
                //return as the final array 
                
                $final_arary["pm bus"] = ["vehicle" => $vehPm, "driver" => $driverPm, "attendant" => $attendantPm,"trip" =>  $tripPm];
            } 
        }


        return response($final_arary);
    }

    public function otherParent($id)
    {
        $parentMain = User::find($id);

        $others = User::where('linked_to','=', $parentMain->id)->get()->except('password');

        return response($others);
    }

    public function review(Request $request, $id)
    {
        $json = json_decode($request->getContent(), true);

        $parent = User::find($id);

        if (! $parent) {
            return abort(404, 'not found');
        }

        $review = new Review();
        $review->user_id = $parent->id;
        $review->student_id = $json['student_id'];
        $review->trip_id = $json['trip_id'];
        $review->rating = $json['rating'];
        $review->feedback = $json['feedback'];

        if ($review->save()) {
            return response(1);
        }
        return response(0);
    }
}
