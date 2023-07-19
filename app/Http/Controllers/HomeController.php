<?php

namespace App\Http\Controllers;

use App\Models\DriverLicence;
use App\Models\Inspection;
use App\Models\Insurance;
use App\Models\Invoice;
use App\Models\NotificationSetting;
use App\Models\Route;
use App\Models\SAndT;
use App\Models\SchoolTermDate;
use App\Models\Settings;
use App\Models\Student;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Zone;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use stdClass;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();

        $parents = count(User::where('user_type','=','parent')->get());

        $home_parents = view('home', compact('parents'));

        if ($user->user_type == 'office staff') {
            $settings = Settings::find(1) ?? '';

            if (! $settings) {
                return redirect()->route('first_settings')->with('unsuccess', 'Please register system settings');
            }

            return $home_parents;
        } else {
            $settings = Settings::find(1);

            if (! $settings) {
                return redirect()->route('first_settings')->with(['unsuccess' => 'Please register system settings', 'settings' => $settings]);
                
            }

            return $home_parents;
        }

        if ($user->user_type == 'parent') {
        
            $parent = Auth::user();

            return view('phome');
        }
        
    }

    public function changepass()
    {
        $parent = Auth::user();

        $pNofitications = User::find($parent->id)->unreadNotifications;

        $numOfNotifications = count($pNofitications);

        return view('parents.changepassword')->with([
            'pNofitications' => $pNofitications,
            'numOfNotifications' => $numOfNotifications
        ]);
    }


    public function changePassword(Request $request)
    {
        
        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
        }
    
        if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
            //Current password and new password are same
            return redirect()->back()->with("error","New Password cannot be same as your current password. Please choose a different password.");
        }
    
        $validatedData = $request->validate([
            'current-password' => 'required',
            'new-password' => 'required|string|min:6',
        ]);
    
        //Change Password
        $user = User::find(Auth::user()->id);
        $user->password = bcrypt($request->get('new-password'));
        $user->password_changed = 1;
        $user->update();

        
        if (Auth::user()->user_type == 'teacher') {
            return redirect()->route('schoolattcreate')->with("success","Password changed successfully !");
        } else if (Auth::user()->user_type == 'parent') {
            return redirect()->route('phome')->with("success","Password changed successfully !");
        }else if (Auth::user()->user_type == 'driver') {
            return redirect()->route('driverlogin_home')->with("success","Password changed successfully !");
        } else {
            return redirect()->route('home')->with("success","Password changed successfully !");
        }
        
    }

    public function personalToken()
    {
        return view('personaltoken.index');
    }


    public function headerData()
    {
        
    }

    public function parentsChildLocationProvided()
    {
        $students = Student::whereNotNull('lat')->orWhereNotNull('lat_drop')->orderBy('created_at','desc')->take(10)->get();

        $final_array = [];

        foreach ($students as $key => $student) {
            $student_trip = SAndT::where('student_id','=', $student->id)->get();
            if ($student_trip->isEmpty()) {
                $obj = new stdClass;
                $obj->student_name = $student->first_name .' '.$student->last_name;
                $obj->parent_name = $student->parent->name;
                $obj->contact = $student->parent->phone_num;
                array_push($final_array, $obj);
            }
        }

        return response($final_array);
    }


    public function chartData()
    {
        

    }


    public function officeStaff()
    {
        $officeStaffs = User::where('user_type', 'LIKE', 'parent')->where('using_app', '=', 1)->take(4)->get();

        $id = 1;
        $table = '';
        foreach ($officeStaffs as $officeStaff) {

            $table .= '<tr><td>' . $id++ .'</td>' . '<td>'. $officeStaff->name .'</td>' . '<td>'.$officeStaff->email.'</td>' . '<td>'. $officeStaff->phone_num .'</td><td>'. $officeStaff->id_num .'</td></tr>';
            
        }

        return response($table);
    }



    public function vehicleStaffNum()
    {
        $date = date('Y-m');


        $num = count(User::where('user_type', '=', 'parent')->where('created_at', 'LIKE', '%'. $date . '%')->get());

        $vehicleNum = count(Vehicle::all());


        return response(['new_customer' => $num, 'vehicle_num' => $vehicleNum]);
    }

    /**
     * new home data
     */
    public function firstTopTab()
    {
        $drivers = count(User::where('user_type', 'LIKE', 'driver')->get());
        $vehicles = count(Vehicle::all());
        $students = count(Student::all());
        $student_transport = count(Student::where('transport','=', 1)->get());
        $staff = count(User::where('user_type', '=', 'office staff')
                ->orWhere('user_type', '=', 'admin')
                ->orWhere('user_type', '=', 'supervisor')
                ->orWhere('user_type', '=', 'head teacher')
                ->orWhere('user_type', '=', 'director')
                ->orWhere('user_type', '=', 'teacher')
                ->get());
        $parents = count(User::where('user_type','=','parent')->get());
        $routes = count(Route::all());
        $zones = count(Zone::all());

        return response([
            'drivers'=>$drivers,
            'vehicles' => $vehicles, 
            'students' => $students, 
            'student_transport' => $student_transport, 
            'staff' => $staff, 
            'parents' => $parents,
            'routes' => $routes,
            'zones' => $zones
        ]);
    }

    public function getMonthListFromDate(Carbon $start, Carbon $end)
    {
        $start->setDay(1);
        foreach (CarbonPeriod::create($start, '1 month', $end) as $key => $month) {
            $months[$key] = $month->format('m');
        }
        return $months;
    }

    public function systemWarnings()
    {
        $notificationSetting = NotificationSetting::find(1);
        $alerts = [];

        if ($notificationSetting) {
            $today = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
            $vehicles = Vehicle::where('status','=',1)->get();
            foreach ($vehicles as $key => $vehicle) {
                $inspection = Inspection::where('vehicle_id','=', $vehicle->id)->orderBy('created_at','desc')->first();
                if ($inspection) {
                    $days_to_send_before_exp = Carbon::createFromFormat('Y-m-d', $inspection->next_inspection)->subDays($notificationSetting->inspection_send_at);
                    if ($days_to_send_before_exp->eq($today)) {
                        $alert_text = "Vehicle inspections are approaching.";
                        array_push($alerts, $alert_text);
                    }
                }

                $insurance = Insurance::where('vehicle_id','=',$vehicle->id)->where('status','=',1)->first();
                if ($insurance) {
                    $days_to_send_before_exp = Carbon::createFromFormat('Y-m-d', $insurance->exp_date)->subDays($notificationSetting->insurance_send_at);
                    $today = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
    
                    if ($days_to_send_before_exp->eq($today)) {
                        $alert_text_insurance = "Vehicle insurance are approaching expiration.";
                        array_push($alerts, $alert_text_insurance);
                    }
                }
            }


            //driver licenses
            $drivers = User::where('user_type','=','driver')->where('status','=',1)->get();
            foreach ($drivers as $key => $driver) {
                $licence = DriverLicence::where('driver_id','=', $driver->id)->first();
                if ($licence) {
                    $days_to_send_before_exp = Carbon::createFromFormat('Y-m-d', $licence->exp_date)->subDays($notificationSetting->dl_send_at);
                    $today = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
    
                    if ($days_to_send_before_exp->eq($today)) {
                        $alert_text_license = "Check on driver licenses, some are nearing expiration.";
                        array_push($alerts, $alert_text_license);
                    }
                }
            }
        }
        
        return response($alerts);
    }

    public function pusherBeams()
    {
        return view('test-tracker');
    }

    public function pusherBeamsTest()
    {
        return view('test-beams');
    }
}
