<?php

namespace App\Http\Controllers\api\v1;

use AfricasTalking\SDK\AfricasTalking;
use App\Events\NewMessageNotification;
use App\Events\NewNotification;
use App\Events\VehicleLocation;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\DepatureChecklist;
use App\Models\FlagOff;
use App\Models\Incident;
use App\Models\IncidentImages;
use App\Models\Inspection;
use App\Models\NotificationSetting;
use App\Models\PickupPoint;
use App\Models\PickupPointStudent;
use App\Models\ReturnChecklist;
use App\Notifications\BusLate;
use App\Notifications\HereNotification;
use App\Notifications\StartNotification;
use App\Notifications\StopNotification;
use App\Notifications\VehicleOutOfSchool;
use App\Models\SAndT;
use App\Models\SchoolTermDate;
use App\Models\SchoolTrip;
use App\Models\Settings;
use App\Models\SmsSetting;
use App\Models\StandinBus;
use App\Models\StandinDriver;
use App\Models\Student;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\SchoolTripDepatureNotification;
use App\Notifications\SchoolTripGoingBackNotification;
use App\Notifications\SchoolTripReachedDestNotification;
use App\Notifications\SchoolTripReachedSchoolNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Pusher\PushNotifications\PushNotifications;
use stdClass;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $login = $request->validate([
            'email'=> 'required|string',
            'password'=> 'required|string'
        ]);


        if (! Auth::attempt($login)) {
            return abort(401);
        }

        $user = Auth::user();
        if ($user->image) {
            $user->avatar = asset('store/'.$user->image);
        } else {
            if ($user->gender == 'male') {
                $user->avatar = 'https://cdn-icons-png.flaticon.com/512/9875/9875255.png';
            } else {
                $user->avatar = 'https://cdn-icons-png.flaticon.com/512/9875/9875392.png';
            }
        }

        $accessToken = $user->createToken('authToken')->accessToken;

        if (! $user->using_app) {
            $user = User::find($user->id);
            $user->using_app = 1;
            $user->update();
        }
        
        return response(['user' => Auth::user(), 'access_token' => $accessToken]);
    }

    public function logout()
    {
        Auth::user()->tokens->each(function($token, $key) {
            $token->delete();
        });
    
        return response()->json('Successfully logged out');
    }


    public function authenticated()
    {
        if(Auth::guard('api')->check()){
            return response(['msg' => 'authenticated']);
        } else {
            return abort(401);
        }
    }

    public function getUser()
    {
        $user = auth('api')->user();
        if ($user->image) {
            $user->avatar = asset('store/'.$user->image);
        } else {
            if ($user->gender == 'male') {
                $user->avatar = 'https://cdn-icons-png.flaticon.com/512/9875/9875255.png';
            } else {
                $user->avatar = 'https://cdn-icons-png.flaticon.com/512/9875/9875392.png';
            }
        }
       
        return response(['user' => $user]);
    }

    /**
     * 
     * schoool details
     */
    public function schoolDetails()
    {
        $settings = Settings::find(1);

        if (! $settings) {
            return abort(404, 'not found');
        }

        $image = null;

        if ($settings->image) {
            $image = asset('store/'.$settings->company_logo);
        }

        $settingsArray = [
            'company_name' => $settings->company_name ?? '',
            'company_pnum' => $settings->company_pnum ?? '',
            'company_address' => $settings->company_address ?? '',
            'currency' => $settings->currency ?? '',
            'school_loc_lat' => $settings->lat ?? '',
            'school_loc_lng' => $settings->lng ?? '',
            'time_zone' => $settings->time_zone ?? '',
            'company_logo' => $image,
        ];

        return response($settingsArray);
    }

    
    
    public function getTrips()
    {
        $user = auth('api')->user();
        $vehicle = Vehicle::where('driver_id', '=', $user->id)->first() ?? Vehicle::where('attendant_id', '=', $user->id)->first() ?? null;
        //driver stand-in
        if ($user->user_type == "driver") {
            $check_stand_in_driver = StandinDriver::where('stand_in_driver','=', $user->id)->where('status','=', 1)->first();
            if ($check_stand_in_driver) {
                $vehicle = Vehicle::where('id', '=', $check_stand_in_driver->stand_in_vehicle)->first();
                if (!$vehicle) {
                    return abort(404, 'vehicle not found/not allocated');
                }
                return response($vehicle->trips);
            }
        }

        //attendant stand-in
        if ($user->user_type == "attendant") {
            $check_stand_in_attendant = StandinDriver::where('stand_in_attendant','=', $user->id)->where('status','=', 1)->first();
            if ($check_stand_in_attendant) {
                $vehicle = Vehicle::where('id', '=', $check_stand_in_attendant->stand_in_vehicle)->first();
                if (!$vehicle) {
                    return abort(404, 'vehicle not found/not allocated');
                }
                return response($vehicle->trips);
            }
        }

        
        //kama driver ame login we check if his bus hio day ni stand in
        //if yes we take the original vehicle and send relevant data
        if (!$vehicle) {
            return abort(404, 'vehicle not found/not allocated');
        }

        //vehicle stand-in
        $check_stand_in = StandinBus::where('stand_in_vehicle','=', $vehicle->id)->where('status','=', 1)->first();
        if ($user->user_type == "driver" && $check_stand_in) {
            $original_vehicle = Vehicle::find($check_stand_in->original_vehicle);
            $trips = $original_vehicle->trips;
            return response($trips);
        }


        $trips = $vehicle->trips;

        return response($trips);
    }

    public function getStudents($trip_id) 
    {
        $user = auth('api')->user();

        $students = [];
        
        $finalstd = [];
        
        $sandt = SAndT::where('trip_id', '=', $trip_id)->get();

        $trip = Trip::find($trip_id);

        foreach ($sandt as $s) {
            $students[] = Student::where('id', '=', $s->student_id)->get();
        }
        for ($i=0; $i < count($students); $i++) { 
            if (!empty($students[$i][0])) {
                if ($students[$i][0]->image) {
                    $image = asset('store/'.$students[$i][0]->image);
                } else {
                    if ($students[$i][0]->gender == 'male') {
                        $image = 'https://cdn-icons-png.flaticon.com/512/3135/3135755.png';
                    } else {
                        $image = 'https://cdn-icons-png.flaticon.com/512/9676/9676572.png';
                    }
                }
                $parent = User::where('id','=', $students[$i][0]->parent_id)->first();

                $today = date('Y-m-d');
                $vehicle = Vehicle::find($trip->vehicle_id);

                $flagoff = FlagOff::where('student_id','=', $students[$i][0]->id)->where('date','=', $today)->first();

                $flagoffReason = $flagoff->reason ?? null;
                $inner_array = [
                    'id' => $students[$i][0]->id,
                    'vehicle_id' => $vehicle->id ?? null,
                    'parent_id' => $parent->id ?? null,
                    'parent_name' => $parent->name ?? null,
                    'parent_phone' => $parent->phone_num ?? null,
                    'first_name' => $students[$i][0]->first_name ?? null,
                    'last_name' => $students[$i][0]->last_name ?? null,
                    'to_pick_up' => $students[$i][0]->pick_up,
                    'grade' => $students[$i][0]->grade ?? null,
                    'lat' => $students[$i][0]->lat ?? null,
                    'lng' => $students[$i][0]->lng ?? null,
                    'lat_drop' => $students[$i][0]->lat_drop ?? null,
                    'lng_drop' => $students[$i][0]->lng_drop ?? null,
                    'image' => $image ?? null,
                    'flagoff' => $flagoff
                ];
                array_push($finalstd, $inner_array);
            }  
        }
        //impliment check if there is a flag off so as to add to the list 
        return response($finalstd);
    }


    public function myStudentCount()
    {
        $user = auth('api')->user();

        $vehicle = Vehicle::where('driver_id', '=', $user->id)->first() ?? abort(404, 'not found');

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

        $check_stand_in = StandinBus::where('stand_in_vehicle','=', $vehicle->id)->where('status','=', 1)->first();
        if ($user->user_type == "driver" && $check_stand_in) {
            $vehicle = Vehicle::find($check_stand_in->original_vehicle);
        }


        $numOfStudents = 0;

        $time = date('a');

        if ($vehicle) {
            if ($time == "am") {
                $numOfStudents = count(DB::table('vehicle_students')->where('vehicle_id','=',$vehicle->id)->where('route_time','=', 'am')->get());
            } else {
                $numOfStudents = count(DB::table('vehicle_students')->where('vehicle_id','=',$vehicle->id)->where('route_time','=', 'pm')->get());
            }
            
        } 

        return response(['num of students' => $numOfStudents]);
    }

    public function getVehicle()
    {
        $user = auth('api')->user();

        $notificationSetting = NotificationSetting::find(1);

        if (!$notificationSetting) {
            return response("not assigned vehicle");
        }

        $vehicle = Vehicle::where('driver_id', '=', $user->id)->first() ?? Vehicle::where('attendant_id', '=', $user->id)->first();

        
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

        $vehicle->pickup_notification_distance = $notificationSetting->value;
        $vehicle->distance_interval = 0;
        if (! $vehicle) {
            return abort(404,'Vehicle not found or allocated');
        }
        return response(['vehicle' => $vehicle]);
    }

    public function getParent($id) 
    {
        $student = Student::find($id);

        if (! $student) {
            return abort(404,"not found");
        }
        $parent = User::where('id', '=', $student->parent_id)->first();

        if (! $parent) {
            return abort(404,"not found");
        }

        return response(['parent' => $parent]);
    }

    public function saveNotification()
    {
        $driver = auth('api')->user();

        $vehicle = Vehicle::where('driver_id', '=', $driver->id)->first() ?? abort(404, 'not found');

        $users = User::where('user_type', 'LIKE', 'office staff')
                        ->orWhere('user_type', 'LIKE', 'admin')
                        ->orWhere('user_type', 'LIKE', 'supervisor')
                        ->orWhere('user_type', 'LIKE', 'manager')
                        ->orWhere('user_type', 'LIKE', 'office_executive')
			            ->orWhere('user_type', 'LIKE', 'parent')
                        ->get();

        $settings = Settings::find(1);

        $pushNotifications = new PushNotifications([
            "instanceId" => "6ef6b376-22d2-4faf-987c-84ce1dbb7999",
            "secretKey" => "AB20F2E76BC23EE6199124A35254E4CD76B91F39E8C584626EEA8917C4FD2461",
        ]);
        
        Notification::send($users, new VehicleOutOfSchool($vehicle->title, $vehicle->plate_num));
        foreach ($users as $user) {
            $publishResponse = $pushNotifications->publishToInterests(
                ['transport-'.$user->id],
                [
                    "fcm" => [
                        "notification" => [
                            "title" => "Vehicle Out Of School",
                            "body" => "Vehicle ($vehicle->title $vehicle->plate_num) is out of school.  Driver Name: $driver->name, Contact:  $driver->phone_num.",
                            "icon" => asset('store/'.$settings->company_logo),
                        ],
                    ],
                    "web" => [
                        "time_to_live" => 3600,
                        "notification" => [
                            "title" => "Vehicle Out Of School",
                            "body" => "Vehicle ($vehicle->title $vehicle->plate_num) is out of school.  Driver Name: $driver->name, Contact:  $driver->phone_num.",
                            "icon" => asset('store/'.$settings->company_logo),
                            "deep_link" => url('/notification/seenotify'), //url to take user when clicked the notification
                            "hide_notification_if_site_has_focus" => true
                        ]
                    ]
                ]
            );
        }


        return response(['msg'=>'notification sent']);
    }

    /**
     * @param Request
     * @return response 
     */
    public function saveCoords(Request $request)
    {
        $driver = auth('api')->user();

        $vehicle = Vehicle::where('driver_id', '=', $driver->id)->first() ?? abort(404, 'not found');

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

        $vehicle->latitude = $request->latitude;
        $vehicle->longitude = $request->longitude;
        $vehicle->head = $request->head;
        $vehicle->speed = $request->speed;
        $vehicle->update();

        $obj = new stdClass;
        $obj->heading = $request->head;
        $obj->speed = $request->speed;
        $obj->latitude = $request->latitude;
        $obj->longitude = $request->longitude;
        $obj->is_mocked = true;

        Log::error($request);

        event(new VehicleLocation((string)$request->latitude, (string)$request->longitude, (string)$vehicle->id, (string)$request->speed, (string)$request->head));
        
        if($vehicle->update()) {
            return response(['msg' => 'updated']);
        } else {
            return abort(404);
        }
    }

    public function sendStart($id)
    {
        $staffs = User::where('user_type', 'LIKE', 'office staff')
                        ->orWhere('user_type', 'LIKE', 'admin')
                        ->orWhere('user_type', 'LIKE', 'supervisor')
                        ->orWhere('user_type', 'LIKE', 'head teacher')
                        ->orWhere('user_type', 'LIKE', 'director')
                        ->get();

        $settings = Settings::find(1);

        $driver = auth('api')->user();

        $vehicle = Vehicle::where('driver_id', '=', $driver->id)->first() ?? abort(404, 'not found');

        Notification::send($staffs, new StartNotification($driver->name, $vehicle->plate_num, $vehicle->title, $driver->phone_num));
        
        $trip = Trip::where('id','=',$id)->first();

        $trip_students = SAndT::where('trip_id','=', $trip->id)->get();
        /*
        DB::table('vehicle_trip_count')->insert([
            'vehicle_id' => $vehicle->id,
            'trip_id' => $trip->id
        ]);
        */
        $pushNotifications = new PushNotifications([
            "instanceId" => "6ef6b376-22d2-4faf-987c-84ce1dbb7999",
            "secretKey" => "AB20F2E76BC23EE6199124A35254E4CD76B91F39E8C584626EEA8917C4FD2461",
        ]);

        foreach ($trip_students as $key => $trip_student) {
            $student = Student::find($trip_student->student_id);
            $user = User::where('id', '=',$student->parent_id)->first();
            Notification::send($user, new StartNotification($driver->name, $vehicle->plate_num, $vehicle->title, $driver->phone_num));

            //event(new NewMessageNotification($user->id, 'Start Trip', "Vehicle for $student->first_name $student->last_name has commenced its trip from school"));
        
            $publishResponse = $pushNotifications->publishToInterests(
                ['transport-'.$user->id],
                [
                    "fcm" => [
                        "notification" => [
                            "title" => "Start Trip",
                            "body" => "Vehicle ($vehicle->title $vehicle->plate_num) has commenced its trip, Driver Name: $driver->name, Contact:  $driver->phone_num.",
                            "icon" => asset('store/'.$settings->company_logo),
                        ],
                    ],
                    "web" => [
                        "time_to_live" => 3600,
                        "notification" => [
                            "title" => "Start Trip",
                            "body" => "Vehicle ($vehicle->title $vehicle->plate_num) has commenced its trip, Driver Name: $driver->name, Contact:  $driver->phone_num.",
                            "icon" => asset('store/'.$settings->company_logo),
                            "deep_link" => url('/notification/seenotify'), //url to take user when clicked the notification
                            "hide_notification_if_site_has_focus" => true
                        ]
                    ]
                ]
            );
        
        }

        return response(['msgs'=>'notification sent']);        
    }
    //done
    public function sendStop($id)
    {
        $settings = Settings::find(1);

        $pushNotifications = new PushNotifications([
            "instanceId" => "6ef6b376-22d2-4faf-987c-84ce1dbb7999",
            "secretKey" => "AB20F2E76BC23EE6199124A35254E4CD76B91F39E8C584626EEA8917C4FD2461",
        ]);

        $users = User::where('user_type', 'LIKE', 'office staff')
        ->orWhere('user_type', 'LIKE', 'admin')
        ->orWhere('user_type', 'LIKE', 'supervisor')
        ->orWhere('user_type', 'LIKE', 'manager')
        ->orWhere('user_type', 'LIKE', 'office_executive')
        ->get();

        $driver = auth('api')->user();

        $vehicle = Vehicle::where('driver_id', '=', $driver->id)->first() ?? abort(404, 'not found');;

        Notification::send($users, new StopNotification($driver->name, $vehicle->plate_num, $vehicle->title, $driver->phone_num));
        //msg' => "Vehicle ($this->vehicle_title $this->vehicle_reg) has conculed its trip, Driver Name: $this->driver_name, Contact: $this->driver_phone
        foreach ($users as $user) {
            //event(new NewMessageNotification($user->id, 'Start Trip', "Vehicle $vehicle->title has concluded its trip from school"));
        
            $publishResponse = $pushNotifications->publishToInterests(
                ['transport-'.$user->id],
                [
                    "fcm" => [
                        "notification" => [
                            "title" => "End Trip",
                            "body" => "Vehicle ($vehicle->title $vehicle->plate_num) has concluded its trip, Driver Name: $driver->name, Contact: $driver->phone_num.",
                            "icon" => asset('store/'.$settings->company_logo),
                        ],
                    ],
                    "web" => [
                        "time_to_live" => 3600,
                        "notification" => [
                            "title" => "End Trip",
                            "body" => "Vehicle ($vehicle->title $vehicle->plate_num) has concluded its trip, Driver Name: $driver->name, Contact: $driver->phone_num.",
                            "icon" => asset('store/'.$settings->company_logo),
                            "deep_link" => url('/notification/seenotify'), //url to take user when clicked the notification
                            "hide_notification_if_site_has_focus" => true
                        ]
                    ]
                ]
            );
        }
        $trip = Trip::where('id','=',$id)->first();
        
        $trip_students = SAndT::where('trip_id','=', $trip->id)->get();

        foreach ($trip_students as $key => $trip_student) {
            $student = Student::find($trip_student->student_id);
            $parent = User::where('id', '=',$student->parent_id)->first();
            Notification::send($parent, new StopNotification($driver->name, $vehicle->plate_num, $vehicle->title, $driver->phone_num));
            //event(new NewMessageNotification($parent->id, 'Start Trip', "Vehicle $vehicle->title has concluded its trip from school"));
            //Vehicle ($vehicle->title $vehicle->plate_num) has concluded its trip, Driver Name: $driver->name, Contact: $driver->phone_num.
            $publishResponse = $pushNotifications->publishToInterests(
                ['transport.'.$parent->id],
                [
                    "fcm" => [
                        "notification" => [
                            "title" => "End Trip",
                            "body" => "Vehicle ($vehicle->title $vehicle->plate_num) has concluded its trip, Driver Name: $driver->name, Contact: $driver->phone_num.",
                            "icon" => asset('store/'.$settings->company_logo),
                        ],
                    ],
                    "web" => [
                        "time_to_live" => 3600,
                        "notification" => [
                            "title" => "End Trip",
                            "body" => "Vehicle ($vehicle->title $vehicle->plate_num) has concluded its trip, Driver Name: $driver->name, Contact: $driver->phone_num.",
                            "icon" => asset('store/'.$settings->company_logo),
                            "deep_link" => url('/notification/seenotify'), //url to take user when clicked the notification
                            "hide_notification_if_site_has_focus" => true
                        ]
                    ]
                ]
            );
        
        }

        return response(['msg'=>'notification sent']);
    }
    /*
    //done
    public function sendHere(Request $request)
    {
        $json = json_decode($request->getContent(), true);

        $num = count($json);

        for ($i=0; $i < $num; $i++) { 
            $student = Student::find($json[$i]['id']);

            $parent = User::where('id', '=', $student->parent_id)->first();

            Notification::send($parent, new HereNotification());
        }

        return response(['msg'=>'notification sent']);
    }
    impliment this as here notification not the one above
    */
    public function sendHere($id)
    {
        $pickupPoint = PickupPoint::find($id);
        $pickupPointStudents = PickupPointStudent::where('id','=', $pickupPoint->id)->get();
        $parents_array = [];

        $settings = Settings::find(1);
        
        $pushNotifications = new PushNotifications([
            "instanceId" => "6ef6b376-22d2-4faf-987c-84ce1dbb7999",
            "secretKey" => "AB20F2E76BC23EE6199124A35254E4CD76B91F39E8C584626EEA8917C4FD2461",
        ]);

        foreach ($pickupPointStudents as $key => $pickupPointStudent) {
            $parent = User::where('id','=',$pickupPointStudent->parent_id)->get();

            Notification::send($parent, new HereNotification());
            array_push($parents_array, $parent->id);

            $time = date('a');
            if ($time == "am") {
                $title = "Bus Pick-up";
            } else {
                $title = "Bus Drop-off";
            }
            

            $publishResponse = $pushNotifications->publishToInterests(
                ['transport-'.$parent->id],
                [
                    "fcm" => [
                        "notification" => [
                            "title" => $title,
                            "body" => "Vehicle is at your stop.",
                            "icon" => asset('store/'.$settings->company_logo),
                        ],
                    ],
                    "web" => [
                        "time_to_live" => 3600,
                        "notification" => [
                            "title" => $title,
                            "body" => "Vehicle is at your stop.",
                            "icon" => asset('store/'.$settings->company_logo),
                            "deep_link" => url('/notification/seenotify'), //url to take user when clicked the notification
                            "hide_notification_if_site_has_focus" => true
                        ]
                    ]
                ]
            );

            $other_parent = User::where('user_type','=','other')->where('linked_to','=',$parent->id)->first();

            if ($other_parent) {
                        //send sms
                $sms_instance = SmsSetting::find(1);
                if ($sms_instance) {
                    $AT = new AfricasTalking($sms_instance->user_name, $sms_instance->api_key);
                    $sms = $AT->sms();
                    $recipients = $other_parent->phone_num;
                    $message = "Vehicle is at your stop";
                    if ($sms_instance->user_name == 'sandbox') {
                        $from = '';
                    } else {
                        $from = $sms_instance->short_code;
                    }
                    try {
                        $sms->send([
                            'to'      => $recipients,
                            'message' => $message,
                            'from'    => $from
                        ]);
                    } catch (\Exception $th) {
                        Log::error($th->getMessage());
                    }
                }
            }
        }/*
        $student = Student::find($id);
        if (! $student) {
            return response(0);
        }
        $parent = User::where('id', '=', $student->parent_id)->first();
        if (! $parent) {
            return response(0);
        }
        if (date('a') == 'am') {
            event(new NewMessageNotification($parent->id, "Pickup for $student->first_name", 'Vehicle is at your stop'));
        } 
        if (date('a') == 'pm') {
            event(new NewMessageNotification($parent->id, "Drop off for for $student->first_name", 'Vehicle is at your stop'));
        }
        */
        

        return response(['msg'=>'notification sent']);
    }


    public function sendLateNotification($id)
    {
        $driver = auth('api')->user();

        $vehicle = Vehicle::where('driver_id', '=', $driver->id)->first() ?? abort(404, 'not found');;

        $trip = Trip::where('id','=',$id)->first();
        
        $trip_students = SAndT::where('trip_id','=', $trip->id)->get();

        $settings = Settings::find(1);
        
        $pushNotifications = new PushNotifications([
            "instanceId" => "6ef6b376-22d2-4faf-987c-84ce1dbb7999",
            "secretKey" => "AB20F2E76BC23EE6199124A35254E4CD76B91F39E8C584626EEA8917C4FD2461",
        ]);

        foreach ($trip_students as $key => $trip_student) {
            $student = Student::find($trip_student->student_id);
            $parent = User::where('id', '=',$student->parent_id)->first();
            Notification::send($parent, new BusLate($driver->name, $vehicle->plate_num, $vehicle->title, $driver->phone_num));
            //event(new NewMessageNotification($parent->id, 'Bus Late', "Bus for $student->first_name $student->last_name will be late. Vehicle $vehicle->title ($vehicle->plate_num), Driver Name: $driver->name, Contact: $driver->phone_num"));
            
            //Vehicle ($vehicle->title $vehicle->plate_num) will arrive late, Driver Name: $driver->name, Contact: $driver->phone_num.
            $publishResponse = $pushNotifications->publishToInterests(
                ['transport-'.$parent->id],
                [
                    "fcm" => [
                        "notification" => [
                            "title" => "Late Bus",
                            "body" => "Vehicle ($vehicle->title $vehicle->plate_num) will arrive late, Driver Name: $driver->name, Contact: $driver->phone_num.",
                            "icon" => asset('store/'.$settings->company_logo),
                        ],
                    ],
                    "web" => [
                        "time_to_live" => 3600,
                        "notification" => [
                            "title" => "Late Bus",
                            "body" => "Vehicle ($vehicle->title $vehicle->plate_num) will arrive late, Driver Name: $driver->name, Contact: $driver->phone_num.",
                            "icon" => asset('store/'.$settings->company_logo),
                            "deep_link" => url('/notification/seenotify'), //url to take user when clicked the notification
                            "hide_notification_if_site_has_focus" => true
                        ]
                    ]
                ]
            );
        }

        return response(['msg'=>'notification sent']);
    }

    /**
     * school trip start notification
     */

    public function sendStartSchoolTrips($id)
    {
        
        $schooltrips = SchoolTrip::find($id) ?? abort(404, 'not found');;

        $depatures = DepatureChecklist::where('schooltrip_id', '=', $schooltrips->id)->get();

        $settings = Settings::find(1);
        
        $pushNotifications = new PushNotifications([
            "instanceId" => "6ef6b376-22d2-4faf-987c-84ce1dbb7999",
            "secretKey" => "AB20F2E76BC23EE6199124A35254E4CD76B91F39E8C584626EEA8917C4FD2461",
        ]);

        foreach ($depatures as $depature) {
            $students = Student::where('id', '=', $depature->student_id)->get();
             foreach ($students as $student) {
                $parent = User::where('id', '=', $student->parent_id)->first();

                Notification::send($parent, new SchoolTripDepatureNotification($schooltrips->trip_name));

                //event(new NewMessageNotification($parent->id, "Start $schooltrips->trip_name School Trip", "$student->first_name has departed from school for $schooltrips->trip_name School Trip"));
                $publishResponse = $pushNotifications->publishToInterests(
                    ['transport-'.$parent->id],
                    [
                        "fcm" => [
                            "notification" => [
                                "title" => "School Trip Departure",
                                "body" => "students have left for the trip to $schooltrips->trip_name.",
                                "icon" => asset('store/'.$settings->company_logo),
                            ],
                        ],
                        "web" => [
                            "time_to_live" => 3600,
                            "notification" => [
                                "title" => "School Trip Departure",
                                "body" => "students have left for the trip to $schooltrips->trip_name.",
                                "icon" => asset('store/'.$settings->company_logo),
                                "deep_link" => url('/notification/seenotify'), //url to take user when clicked the notification
                                "hide_notification_if_site_has_focus" => true
                            ]
                        ]
                    ]
                );
            
            }
            
        }

        return response('Departure notification sent');
    }

    /**
     * school trip reached destination notif
     */
    public function sendReachedDestination($id)
    {
        $schooltrips = SchoolTrip::find($id) ?? abort(404, 'not found');;

        $depatures = DepatureChecklist::where('schooltrip_id', '=', $id)->get();

        $settings = Settings::find(1);
        
        $pushNotifications = new PushNotifications([
            "instanceId" => "6ef6b376-22d2-4faf-987c-84ce1dbb7999",
            "secretKey" => "AB20F2E76BC23EE6199124A35254E4CD76B91F39E8C584626EEA8917C4FD2461",
        ]);

        foreach ($depatures as $depature) {
            $students = Student::where('id', '=', $depature->student_id)->get();
             foreach ($students as $student) {
                $parent = User::where('id', '=', $student->parent_id)->first();

                Notification::send($parent, new SchoolTripReachedDestNotification());
                //event(new NewMessageNotification($parent->id, "Reached destination $schooltrips->trip_name", "$student->first_name has reached  $schooltrips->destination_name"));
            
                $publishResponse = $pushNotifications->publishToInterests(
                    ['transport-'.$parent->id],
                    [
                        "fcm" => [
                            "notification" => [
                                "title" => "School Trip Arrival",
                                "body" => "Students reached school trip destination.",
                                "icon" => asset('store/'.$settings->company_logo),
                            ],
                        ],
                        "web" => [
                            "time_to_live" => 3600,
                            "notification" => [
                                "title" => "School Trip Arrival",
                                "body" => "Students reached school trip destination.",
                                "icon" => asset('store/'.$settings->company_logo),
                                "deep_link" => url('/notification/seenotify'), //url to take user when clicked the notification
                                "hide_notification_if_site_has_focus" => true
                            ]
                        ]
                    ]
                );
            }
            
        }

        return response('notification sent');
    }
    /**
     * goint gack to school from school trip
     */
    public function sendGoindBack($id)
    {
        $schooltrips = SchoolTrip::find($id) ?? abort(404, 'not found');;

        $depatures = ReturnChecklist::where('schooltrip_id', '=', $id)->get();

        $settings = Settings::find(1);
        
        $pushNotifications = new PushNotifications([
            "instanceId" => "6ef6b376-22d2-4faf-987c-84ce1dbb7999",
            "secretKey" => "AB20F2E76BC23EE6199124A35254E4CD76B91F39E8C584626EEA8917C4FD2461",
        ]);

        foreach ($depatures as $depature) {
            $students = Student::where('id', '=', $depature->student_id)->get();
             foreach ($students as $student) {
                $parent = User::where('id', '=', $student->parent_id)->first();

                Notification::send($parent, new SchoolTripGoingBackNotification());

                $publishResponse = $pushNotifications->publishToInterests(
                    ['transport-'.$parent->id],
                    [
                        "fcm" => [
                            "notification" => [
                                "title" => "School Trip",
                                "body" => "Students going back to school form school trip.",
                                "icon" => asset('store/'.$settings->company_logo),
                            ],
                        ],
                        "web" => [
                            "time_to_live" => 3600,
                            "notification" => [
                                "title" => "School Trip",
                                "body" => "Students going back to school form school trip.",
                                "icon" => asset('store/'.$settings->company_logo),
                                "deep_link" => url('/notification/seenotify'), //url to take user when clicked the notification
                                "hide_notification_if_site_has_focus" => true
                            ]
                        ]
                    ]
                );
            }

            
            
        }

        return response('notification sent');
    }
    /**
     * reached school notif
     */
    public function sendReachedSchool($id)
    {
        $depatures = ReturnChecklist::where('schooltrip_id', '=', $id)->get();

        $settings = Settings::find(1);
        
        $pushNotifications = new PushNotifications([
            "instanceId" => "6ef6b376-22d2-4faf-987c-84ce1dbb7999",
            "secretKey" => "AB20F2E76BC23EE6199124A35254E4CD76B91F39E8C584626EEA8917C4FD2461",
        ]);

        foreach ($depatures as $depature) {
            $students = Student::where('id', '=', $depature->student_id)->get();
             foreach ($students as $student) {
                $parent = User::where('id', '=', $student->parent_id)->first();

                Notification::send($parent, new SchoolTripReachedSchoolNotification());

                //event(new NewMessageNotification($parent->id, "Going back from schoo trip", "$student->first_name is going back to school from schoo trip"));
            
                $publishResponse = $pushNotifications->publishToInterests(
                    ['transport-'.$parent->id],
                    [
                        "fcm" => [
                            "notification" => [
                                "title" => "School Trip",
                                "body" => "Students reached school safely from school trip.",
                                "icon" => asset('store/'.$settings->company_logo),
                            ],
                        ],
                        "web" => [
                            "time_to_live" => 3600,
                            "notification" => [
                                "title" => "School Trip",
                                "body" => "Students reached school safely from school trip.",
                                "icon" => asset('store/'.$settings->company_logo),
                                "deep_link" => url('/notification/seenotify'), //url to take user when clicked the notification
                                "hide_notification_if_site_has_focus" => true
                            ]
                        ]
                    ]
                );
            }
            
        }

        return response('notification sent');
    }

    public function getMarkedAttendance($id)
    {
        $trip = Trip::find($id) ?? abort(404, 'not found');;

        $date = date('Y-m-d');

        $attendace = Attendance::where('route_time','=',$trip->time)->where('date','=',$date)->get();

        $final_array = [];

        foreach ($attendace as $key => $item) {
            $obj = new stdClass;
            $obj->student = $item->student_id;
            $obj->trip_id = $trip->id;

            array_push($final_array, $obj);
        }


        return response($final_array);
    }

    public function allStudents($id)
    {
        $vehicle = Vehicle::find($id) ?? abort(404, 'not found');;

        $veh_students_am = DB::table('vehicle_students')->where('vehicle_id','=',$vehicle->id)->where('route_time','=','am')->get();
        $veh_students_pm = DB::table('vehicle_students')->where('vehicle_id','=',$vehicle->id)->where('route_time','=','pm')->get();
        $final_arr_am = [];
        $final_arr_pm = [];

        foreach ($veh_students_am as $std) {
            $student = Student::find($std->student_id);
            $obj = new stdClass;
            $obj->id = $student->id;
            $obj->first_name = $student->first_name;
            $obj->last_name = $student->last_name;
            $obj->add_num = $student->add_num;
            $obj->lat = $student->lat;
            $obj->lng = $student->lng;

            array_push($final_arr_am, $obj);
        }

        foreach ($veh_students_pm as $stds) {
            $student = Student::find($stds->student_id);
            $objs = new stdClass;
            $objs->id = $student->id;
            $objs->first_name = $student->first_name;
            $objs->last_name = $student->last_name;
            $objs->add_num = $student->add_num;
            $objs->lat = $student->lat;
            $objs->lng = $student->lng;

            array_push($final_arr_pm, $objs);
        }
        
        return response(['am students' => $final_arr_am, 'pm students' => $final_arr_pm]);
    }

    public function addPickupPoint(Request $request, $id)
    {
        $driver = auth('api')->user();

        $vehicle = Vehicle::find($id);

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
            return abort(404,'not found');
        }

        $json = json_decode($request->getContent(), true);

        $term = SchoolTermDate::where('status','=', 1)->first();

        if (! $term) {
            return abort('404', 'Term not created');
        }

        $term = $term->id;

        DB::transaction(function() use($json, $vehicle, $term) {
            
            $pickupPoint = new PickupPoint;
            $pickupPoint->term_id = $term;
            $pickupPoint->location_name = $json["location_name"];
            $pickupPoint->vehicle_id = $vehicle->id;
            $pickupPoint->lat = $json["lat"];
            $pickupPoint->lng = $json["lng"];
            $pickupPoint->trip_id = $json["trip_id"];
            $pickupPoint->status = $json["type"] ?? null;
            $pickupPoint->save();

            for ($i=0; $i < count($json["students"]); $i++) { 
                $pickupPointStudents = new PickupPointStudent();
                $pickupPointStudents->pickuppoint_id = $pickupPoint->id;
                $pickupPointStudents->student_id = $json["students"][$i]['student_id'];
                $pickupPointStudents->save();

                if ($json['type'] == 'pickup') {
                    $student = Student::find($json["students"][$i]['student_id']);
                    $student->lat = $pickupPoint->id;
                    $student->lng = $pickupPoint->id;
                    $student->pickup_id = $pickupPoint->id;
                    $student->update();
                } 

                if ($json['type'] == 'dropoff') {
                    $student = Student::find($json["students"][$i]['student_id']);
                    $student->lat = $pickupPoint->id;
                    $student->lng = $pickupPoint->id;
                    $student->dropoff_id = $pickupPoint->id;
                    $student->update();
                }
                
            }
        });
        
        return response('success');
    }


    public function getPickupPoints($id)
    {
        $trip = Trip::find($id);
        
        $pickupPoints = PickupPoint::where('trip_id','=',$trip->id)->get();

        $final_array = [];

        $term = SchoolTermDate::where('status','=', 1)->first();

        foreach ($pickupPoints as $key => $pickupPoint) {
            $renew = 0;
            if ($pickupPoint->term_id != $term->id) {
                $renew = 1;
            }
            $obj = new stdClass;
            $obj->pickup_id = $pickupPoint->id;
            $obj->renew = $renew;
            $obj->vehicle_id = $pickupPoint->vehicle_id;
            $obj->trip_id = $pickupPoint->trip_id;
            $obj->status = $pickupPoint->status;
            $obj->location_name = $pickupPoint->location_name;
            $obj->lat = $pickupPoint->lat;
            $obj->lng = $pickupPoint->lng;
            $inner_array = [];

            $student_points = PickupPointStudent::where('pickuppoint_id','=',$pickupPoint->id)->get();
            $today = date('Y-m-d');

            foreach ($student_points as $student) {
                $std = Student::find($student->student_id);
                $trip = Trip::find($pickupPoint->trip_id);
                $flagoff = FlagOff::where('student_id','=', $std->id)->where('date','=', $today)->first();
                

                $objStd = new stdClass;
                $objStd->student_id = $std->id;
                $objStd->student_name = $std->first_name . ' ' . $std->last_name;
                $objStd->admission = $std->add_num;
                $objStd->flagoff = 0;
                
                if ($flagoff) {
                    if ($flagoff->time == "am" && $trip->time == "am") {
                        $objStd->flagoff = 1;
                    }
    
                    if ($flagoff->time == "pm" && $trip->time == "pm") {
                        $objStd->flagoff = 1;
                    }
    
                    if ($flagoff->time == "full day") {
                        $objStd->flagoff = 1;
                    }
                } 

                if ($std->pick_up == 0) {
                    $objStd->flagoff = 1;
                }

                array_push($inner_array, $objStd);
            };

            $obj->students = $inner_array;

            array_push($final_array, $obj);
        }

        return response($final_array);
    }

        /**
     * update vehcile image
     */
    public function updateVehicleImage(Request $request, $id)
    {
        $vehicle = Vehicle::find($id);

        if (! $vehicle) {
            return response()->json(["message" => "not found"], 401);
        }

        if ($vehicle->image) {
            Storage::disk('public_uploads')->delete($vehicle->image);
        }
        $json = json_decode($request->getContent(), true);

        $file = $json['image'];
        $exploded = explode(',', $file, 2); // limit to 2 parts, i.e: find the first comma
        $encoded = $exploded[1];
        $file = base64_decode($encoded);
        //impliment to get mime type/ extension
        $imageName = 'vehicles/'.Str::random(60).'.'.'png';

        Storage::disk('public_uploads')->put('vehicles/'.$imageName, $file);

        $vehicle->image = $imageName;

        if ($vehicle->update()) {
            return response('success');
        }

        return response()->json(["message" => "not found"], 401);
    }


    public function profilePicture()
    {
        $user = auth('api')->user();

        if($user->image) {
            $image = public_path('store/'.$user->image);
            $imageBase64 = "data:image/png;base64,".base64_encode(file_get_contents($image));
        } else {
            $imageBase64 = null;
        }
        
        return response(['image' => $imageBase64]);
    }

    /**
     * driver attendant  to report incident
     */
    public function reportIncident(Request $request)
    {
        $json = json_decode($request->getContent(), true);

        $user = auth('api')->user();

        $vehicle = Vehicle::where('driver_id','=',$user->id)->first() ?? Vehicle::where('attendant_id','=',$user->id)->first();

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

        //vehicle stand-in
        $check_stand_in = StandinBus::where('stand_in_vehicle','=', $vehicle->id)->where('status','=', 1)->first();

        if ($user->user_type == "driver" && $check_stand_in) {
            $vehicle = Vehicle::find($check_stand_in->original_vehicle);
        }

        

        if(! $vehicle) {
            return abort('404','not found');
        }

        DB::transaction(function() use ($request, $vehicle, $json, $user) {
            $incident = new Incident();
            $incident->user_id = $user->id;
            $incident->vehicle_id = $vehicle->id;
            $incident->source = 'bus';
            $incident->trip = $json['trip_id'];
            $incident->date = $json['date'];
            $incident->type = $json['type'];
            $incident->caused_by = $json['caused_by']; 
            $incident->description = $json['description']; 
            switch ($request->caused_by) {
                case 'student':
                    $incident->student_assulter = $json['assaulter'];
                    break;
                case 'parent':
                    $incident->user_assulter = User::find($json['assaulter'])->id;
                    break;
                case 'attendant':
                    $incident->user_assulter = $vehicle->attendant_id;
                    break;
                case 'driver':
                    $incident->user_assulter = $vehicle->driver_id;
                    break;
            }
            if ($json['video']) {
                $file = $json['video'];
                $exploded = explode(',', $file, 2); // limit to 2 parts, i.e: find the first comma
                $encoded = $exploded[1];
                $file = base64_decode($encoded);
                //impliment to get mime type/ extension
                $imageName = 'incident/'.Str::random(60).'.'.'mp4';  
                Storage::disk('public_uploads')->put($imageName, $file);
                $incident->video = $imageName;
            }
            $incident->save();
            if ($json['image']) {
                $images = new IncidentImages();
                $images->incident_id = $incident->id;
                $file = $json['image'];
                $exploded = explode(',', $file, 2); // limit to 2 parts, i.e: find the first comma
                $encoded = $exploded[1];
                $file = base64_decode($encoded);
                //impliment to get mime type/ extension
                $imageName = 'incident/'.Str::random(60).'.'.'jpeg';  
                Storage::disk('public_uploads')->put($imageName, $file);
                $images->path = $imageName;
                $images->save();
            }
        });
        
        return response('success');
    }

    /**
     * user incidents
     */
    public function getIncidents()
    {
        $user = auth('api')->user();
        $incidents = Incident::where('user_id','=',$user->id)->get();
        $incidents_array = [];
        foreach ($incidents as $key => $incident) {
            $incident_images = [];
            $in = new stdClass;
            $in->trip = Trip::find($incident->trip)->title ?? '';
            $in->date = $incident->date;
            $in->type = $incident->type;
            $in->caused_by = $incident->caused_by;
            if ($in->caused_by == 'student') {
                $student = Student::find($incident->student_assulter);
                $in->assulter = $student->first_name . ' ' . $student->last_name;
            } else {
                $us = User::find($incident->user_assulter);
                $in->assulter = $us->name;
            }
            $in->video = asset('store/'.$incident->video);
            $images = IncidentImages::where('incident_id','=',$incident->id)->first();
            if ($images) {
                $path = asset('store/'.$images->path);
                $in->image = $path;
            } else  {
                $in->image = '';
            }
            $in->description = $incident->description;
           
            array_push($incidents_array, $in);
        }
        return response($incidents_array);
    }

    /**
     * vehicle inspection
     */
    public function getInspection()
    {
        $user = auth('api')->user();
        $vehicle = Vehicle::where('driver_id','=',$user->id)->first() ?? Vehicle::where('attendant_id','=',$user->id)->first();
        $inspections = Inspection::where('vehicle_id','=',$vehicle->id)->get();
        $inspection_array = [];
        foreach ($inspections as $inspection) {
            $obj = new stdClass;
            $obj->last_inspection = $inspection->last_inspection;
            $obj->next_inspection = $inspection->next_inspection;
            $obj->location_name = $inspection->location_name;
            $obj->lat = $inspection->lat;
            $obj->lng = $inspection->lng;
            $obj->office_comment = $inspection->office_comment;
            $obj->driver_comment = $inspection->comment;
            $obj->status = $inspection->status;
            array_push($inspection_array, $obj);
        }
        return response($inspection_array);
    }
}
