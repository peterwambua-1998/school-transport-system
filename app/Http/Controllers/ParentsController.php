<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\DepatureChecklist;
use App\Models\FlagOff;
use App\Models\Invoice;
use App\Notifications\GeneratedPassword;
use App\Notifications\StudentAbsent;
use App\Notifications\ToParent;
use App\Models\Receipt;
use App\Models\SAndT;
use App\Models\SchoolAttendance;
use App\Models\SchoolTermDate;
use App\Models\SchoolTrip;
use App\Models\Settings;
use App\Models\Student;
use App\Models\TermEvent;
use App\Models\TermHoliday;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\ShareAppLinkNotification;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class ParentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parents = User::where('user_type', 'LIKE', 'parent')->orWhere('user_type','=', 'parent two')->get();
        return view('parents.index', compact('parents'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('parents.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->gender == 'select...') {
            return redirect()->back()->with('unsuccess','Select gender');
        }
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'phone_number' => 'required',
            'id_number' => 'required'

        ]);

        $generator = new ComputerPasswordGenerator();

        $generator->setLowercase()->setNumbers(false)->setSymbols(false)->setLength(6);

        $password = $generator->generatePassword();

        
        $parent = new User();
        $parent->name = $request->name;
        $parent->email = $request->email;
        $parent->user_type = 'parent';
        $parent->password = Hash::make($password);
        $parent->phone_num = $request->phone_number;
        $parent->id_num = $request->id_number;
        $parent->gender = $request->gender;
        if ($request->has('image')) {
            $path = $request->file('image')->store('parent','public_uploads'); 
            $parent->image = $path;
        }
        $parent->save();
        

        //Notification::send($parent, new GeneratedPassword($password));

        //post to firebase
        // URL to send the POST request to
        $url = 'https://mfika.projtrac.co.ke/parents/create';

        // Data to send in the request
        $data = [
            'guid' => $parent->id,
            'email' => $request->email,
            'password' => $password,
            'mobile' => $request->phone_number,
            'national_id_number' => $request->id_number,
            'presence' => true
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

        return redirect()->route('parents.index')->with('success', 'Record was added successfully');
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
        $parent = User::find(Crypt::decrypt($id));

        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        return view('parents.edit')->with([
            'parent' => $parent,
            'notifications' => $notifications
        ]);
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
        if ($request->gender == 'select...') {
            return redirect()->back()->with('unsuccess','Please select gender');
        }


        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'phone_num' => 'required',
            'id_num' => 'required'
        ]);

        $parent = User::find($id);
        $parent->name = $request->name;
        $parent->email = $request->email;
        $parent->id_num = $request->id_num;
        $parent->phone_num = '+254'.$request->phone_num;
        $parent->gender = $request->gender;

        if($request->image) {
            if ($parent->image) {
                Storage::disk('public_uploads')->delete($parent->image);
            }
            $path = $request->file('image')->store('parent','public_uploads'); 

            $parent->image = $path;
        }
        if($parent->update()){
            return redirect()->route('parents.index')->with('success', 'Record updated successfully');
        };

        return redirect()->back()->with('unsuccess', 'Sytem error please try again');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $parent = User::find($id);
        $students = Student::where('parent_id','=',$parent->id)->where('status','=',1)->get();
        foreach ($students as $key => $std) {
            if ($std->status == 1) {
                return redirect()->back()->with('unsuccess','Parent has active children');
            }
        }

        $parent->status = 0;
        if ($parent->update()) {
            return redirect()->back()->with('success', 'Record deactivated successfully');
        }
        return redirect()->back()->with('unsuccess', 'System error, try again.');

        /*
        $students = Student::where('parent','=',$parent->id)->get();

        if ($students->isNotEmpty()) {
            DB::transaction(function() use ($parent, $students) {
                $invoices = Invoice::where('parent_id', '=', $parent->id)->get();
                $receipts = Receipt::where('parent_id', '=', $parent->id)->get();
                $students = Receipt::where('parent_id', '=', $parent->id)->get();
                DB::table('incidents')->where('user_assulter','=',$parent->id)->delete();
                DB::table('flag_offs')->where('parent_id','=',$parent->id)->delete();
                DB::table('reviews')->where('parent_id','=',$parent->id)->delete();
                DB::table('fee_payments')->where('parent','=',$parent->id)->delete();
                $notifs = $parent->notifications;
                foreach ($$notifs as $notif) {
                    $notif->delete();
                }
                foreach ($invoices as $invoice) {
                    $invoice->parent_id = null;
                    $invoice->update();
                }
                foreach ($receipts as $receipt) {
                    $receipt->parent_id = null;
                    $receipt->update();
                }
                foreach ($students as $student) {
                    $student->parent_id = null;
                    $student->update();
                }
                foreach ($parent->notifications  as $notification) {
                    $notification->delete();
                }
                $parent->delete();
            });
        } else {
            return redirect()->back()->with('unsuccess', 'Delete all children for this parent');
        }
        */
        
    }

    public function activateParent(Request $request) 
    {
        $parent = User::find($request->parent_id);
        $parent->status = 1;
        if ($parent->update()) {
            return redirect()->back()->with('success', 'Record activated successfully');
        }
        return redirect()->back()->with('unsuccess', 'System error, try again.');
    }


    /**
     * view for parents home page 
    */
    public function phome() {

        $parent = Auth::user();

        $pNofitications = User::find($parent->id)->unreadNotifications;

        $numOfNotifications = count($pNofitications);

        $students = Student::where('parent_id', '=', $parent->id)->get();

    
        return view('parentlogin.home')->with([
            
            'pNofitications' => $pNofitications,
            'numOfNotifications' => $numOfNotifications,
            'students' => $students
            
        ]);
    }


    /**
     * data for parents home page
    */

    public function getHomeData() {
        $parent = Auth::user();

        $students = Student::where('parent_id', '=', $parent->id)->get();


        $unpaidInvoice = Invoice::where('parent_id', '=', $parent->id)
                                ->where('status', 'LIKE','unpaid')
                                ->get();

        $total = 0;

        foreach ($unpaidInvoice as $unpaid) {
            $total += $unpaid->amount;
        }

        $unpaidInvoice = count($unpaidInvoice);

        
        $numChild = count($students);


        return response([
            'students' => $students,
            'numChild' => $numChild,
            'unpaidinvoice' => $unpaidInvoice,
            'total_unpaid' => $total,
        ]);
    }

    public function getLangLong(Request $request)
    {
        $parent = User::find($request->pid);

        $students = Student::where('parent_id', '=', $parent->id)->get();

        

        $loc = ["lat" => [], "lng" => [], "label" => [], "stdlat" => [], 'stdlng' => []];

        foreach ($students as $student) {
            array_push($loc["lat"], $student->vehicle->latitude);
            array_push($loc["lng"], $student->vehicle->longitude);
            array_push($loc["label"], $student->vehicle->title);
            array_push($loc["stdlat"], $student->lat);
            array_push($loc["stdlng"], $student->lng);
        }
        
        return response($loc);
    }

    public function myChildren($id) {
        $students = Student::where('parent_id', '=', $id)->get();

        $numChild = count($students);


        $parent = Auth::user();

        $pNofitications = User::find($parent->id)->unreadNotifications;



        $numOfNotifications = count($pNofitications);

        return view('parentlogin.children')->with([
            'students' => $students,
            'numChild' => $numChild,
            'pNofitications' => $pNofitications,
            'numOfNotifications' => $numOfNotifications
        ]);
    }

    public function paidInv($id){
        $invoices = Invoice::where('parent_id', '=', $id)->get();


        $parent = Auth::user();

        $pNofitications = User::find($parent->id)->unreadNotifications;



        $numOfNotifications = count($pNofitications);

        return view('parentlogin.pinvoice')->with([
            'invoices' => $invoices,
            'pNofitications' => $pNofitications,
            'numOfNotifications' => $numOfNotifications
        ]);
    }

    public function unpaidInv($id)
    {
        $invoices = Invoice::where('parent_id', '=', $id)->get();

        $parent = Auth::user();

        $pNofitications = User::find($parent->id)->unreadNotifications;

       

        $numOfNotifications = count($pNofitications);

        return view('parentlogin.unpinvoice')->with([
            'invoices' => $invoices,
            'pNofitications' => $pNofitications,
            'numOfNotifications' => $numOfNotifications
        ]);
    }

    
    

    public function getNotification()
    {
        $parent = Auth::user();

        $pNofitications = User::find($parent->id)->notifications;

       

        $numOfNotifications = count($pNofitications);

        return view('parents.seenotification')->with([
            'pNofitications' => $pNofitications,
            'numOfNotifications' => $numOfNotifications
        ]);
    }


    public function markAsRead($id)
    {
        $notification = DatabaseNotification::find($id);

        $notification->markAsRead();

        return redirect()->back();
    }

    public function addChildView($id)
    {
        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        $parent = User::find($id);

        $vehicles = Vehicle::all();

        return view('parents.addchild')->with([
            'parent' => $parent,
            'vehicles' => $vehicles,
            'notifications' => $notifications
        ]);
    }

    public function addChild(Request $request)
    {
       
        $request->validate([
            'fname' => 'required|max:255',
            'lname' => 'required|max:255',
            'grade' => 'required',
            'vehicle_id' => 'required'
        ]);


        $student = new Student();
        $student->vehicle_id = $request->vehicle_id;
        $student->parent_id = $request->parent_id;
        $student->first_name = $request->fname;
        $student->last_name  = $request->lname;
        $student->grade  = $request->grade;
        $student->add_num = $request->add_num;
        
        $student->lat = $request->lat;
        $student->lng = $request->lng;

        $trips = $request->trip_id;

        $student->save();
        foreach ($trips as $trip) {
            $stdtrip = new SAndT();
            $stdtrip->student_id = $student->id;
            $stdtrip->trip_id = $trip;
            $stdtrip->save();
        }

        
        
        return redirect()->route('parents.index')->with('success', 'Child added successfully');
        

        
    }

    public function getVehicleTrip ($id) {

        
        $vehicle = Vehicle::find($id);

        //$trips = $vehicle->route->trips;

        $myselect = "<label id='myedittrips'>Select  Trip</label><select multiple  name='trip_id[]' id='myeditselect' class='form-control'>";

        foreach ($vehicle->route->trip as $trips) {
            $myselect .= "<option value='$trips->id'>Title: $trips->title, AM/PM: $trips->time,  From: $trips->time_from,  To: $trips->time_to</option>";
        }

        $myselect .= '</select>';



        return response($myselect);
    }


    public function attendanceView($id)
    {
        $parent = Auth::user();

        $student = Student::find($id);

        $pNofitications = User::find($parent->id)->unreadNotifications;

        $numOfNotifications = count($pNofitications);

        return view('parentlogin.attendance')->with([
            'pNofitications' => $pNofitications,
            'numOfNotifications' => $numOfNotifications,
            'student' => $student
        ]);
    }




    public function getAttendanceData($id)
    {
        $date = date('Y-m');
        $student = Student::find($id);

        $attPresent = 0;

        $num = 1;

        $table = '';

        
        
        

            $present = Attendance::where('present', '=', 1)->where('student_id', '=', $student->id)->where('created_at', 'LIKE', '%'. $date. '%')->get();

            $absent = Attendance::where('present', '=', 0)->where('student_id', '=', $student->id)->where('created_at', 'LIKE', '%'. $date. '%')->get();
            
            $attPresent = count($present);

            $attAbsent = count($absent);

            $presentArr = [];
            foreach ($present as $pre) {

                $final = substr_replace($pre->created_at, '', 11);

                $final .= ' ' . $pre->route_time;
                array_push($presentArr, $final);
            }
            
            $presentString = join(',',$presentArr);


            $absentArr = [];
            foreach ($absent as $abse) {

                $finalabse = substr_replace($abse->created_at, '', 11);

                $finalabse .= ' ' . $abse->route_time;

                array_push($absentArr, $finalabse);
            }
            
            $absentString = join(',',$absentArr);

            $table .= '<tr><td>' . $num++ .'</td>' . '<td>'. $student->first_name . $student->last_name .'</td>' . '<td>'. $student->grade .'</td>' . '<td data-toggle="popover" title="Dates Present" data-content="'. $presentString .'">'.$attPresent.'</td>' . '<td data-toggle="popover" title="Dates Absent" data-content="'. $absentString .'">'. $attAbsent .'</td></tr>';
            
        

        return response($table);
       
    }

    public function getAttendanceDataSchool($id)
    {
        $date = date('Y-m');
        $student = Student::find($id);

        $attPresent = 0;

        $num = 1;

        $table = '';

        
        
        

            $present = SchoolAttendance::where('present', '=', 1)->where('student_id', '=', $student->id)->where('created_at', 'LIKE', '%'. $date. '%')->get();

            $absent = SchoolAttendance::where('present', '=', 0)->where('student_id', '=', $student->id)->where('created_at', 'LIKE', '%'. $date. '%')->get();
            
            $attPresent = count($present);

            $attAbsent = count($absent);

            $presentArr = [];
            foreach ($present as $pre) {

                $final = substr_replace($pre->created_at, '', 11);

                $final .= ' ' . $pre->route_time;
                
                array_push($presentArr, $final);
            }
            
            $presentString = join(',',$presentArr);


            $absentArr = [];
            foreach ($absent as $abse) {

                $finalabse = substr_replace($abse->created_at, '', 11);

                $finalabse .= ' ' . $abse->route_time;

                array_push($absentArr, $finalabse);
            }
            
            $absentString = join(',',$absentArr);

            $table .= '<tr><td>' . $num++ .'</td>' . '<td>'. $student->first_name . $student->last_name .'</td>' . '<td>'. $student->grade .'</td>' . '<td data-toggle="popover" title="Dates Present" data-content="'. $presentString .'">'.$attPresent.'</td>' . '<td data-toggle="popover" title="Dates Absent" data-content="'. $absentString .'">'. $attAbsent .'</td></tr>';
            
        

        return response($table);
    }


    public function getSchooltrips()
    {
        $term = SchoolTermDate::where('status', '=', 1)->first();

       

        if (! $term) {
            return redirect()->back()->with('unsuccess', 'School terms have not been created');
        }

        $parent = Auth::user();

        $students = Student::where('parent_id', '=', $parent->id)->get();
        
        
        $pNofitications = User::find($parent->id)->unreadNotifications;

        $numOfNotifications = count($pNofitications);

        return view('parentlogin.schooltipindex')->with([
            'students' => $students,
            'numOfNotifications' => $numOfNotifications,
            'term' => $term
        ]);
    }

    public function childernListPickDrop()
    {
        $parent = Auth::user();

        $students = Student::where('parent_id', '=', $parent->id)->get();

        $transport_head = User::where('user_type', 'LIKE', 'head transport')->first();

        $pNofitications = User::find($parent->id)->unreadNotifications;

        $numOfNotifications = count($pNofitications);

        if (! $transport_head) {
            $transport_head = '';
        }

        return view('parentlogin.pickupindex')->with([
            'students' => $students,
            'parent' => $parent,
            'numOfNotifications' => $numOfNotifications,
            'transport_head' => $transport_head
        ]);
    }

    public function confirmpage($id)
    {
        $student = Student::find($id);

        $parent = Auth::user();

        $pNofitications = User::find($parent->id)->unreadNotifications;

        $numOfNotifications = count($pNofitications);

        return view('parentlogin.confirmpick')->with([
            'numOfNotifications' => $numOfNotifications,
            'student' => $student,
        ]);

    }

    public function confirmedPickup(Request $request)
    {
        $student = Student::find($request->student_id);
        $student->confirm_pickup_parent = 1;

        if($student->update()){
            return response('Pickup /Drop off confirmed');
        }

        return response('System error please try again');
    }


    public function selectPickupView($id)
    {
        $student = Student::find($id);

        $parent = Auth::user();

        $pNofitications = User::find($parent->id)->unreadNotifications;

        $numOfNotifications = count($pNofitications);

        return view('parentlogin.selectpickup')->with([
            'numOfNotifications' => $numOfNotifications,
            'student' => $student,
        ]);
    }


    public function selectPickupSave(Request $request)
    {
        
        $student = Student::find($request->student_id);

        $student->lat = $request->lat;
        $student->lng = $request->lng;
        $student->pickup_changed = 1;
        $student->confirm_pickup_parent = 1;

        if ($student->update()) {
            return redirect()->route('childernlistpickdrop')->with([
                'success' => 'Pickup/Drop off saved'
            ]);
        }

        return redirect()->back()->with([
            'unsuccess' => 'System error please try again'
        ]);
    }

    
    public function changePickupView($id)
    {
        $student = Student::find($id);

        $parent = Auth::user();

        $pNofitications = User::find($parent->id)->unreadNotifications;

        $numOfNotifications = count($pNofitications);

        return view('parentlogin.changepickup')->with([
            'numOfNotifications' => $numOfNotifications,
            'student' => $student,
        ]);
    }

    public function changeDropOffView($id)
    {
        $student = Student::find($id);

        $parent = Auth::user();

        $pNofitications = User::find($parent->id)->unreadNotifications;

        $numOfNotifications = count($pNofitications);

        return view('parentlogin.selectdropoff')->with([
            'numOfNotifications' => $numOfNotifications,
            'student' => $student,
        ]);
    }

    public function changePickupSave(Request $request)
    {
        $student = Student::find($request->student_id);

        $student->lat = $request->lat;
        $student->lng = $request->lng;
        $student->pickup_changed = 1;
        $student->confirm_pickup_parent = 1;

        if ($student->update()) {
            return redirect()->route('changeDropOffView', $student->id)->with([
                'success' => 'Pickup saved successfully'
            ]);
        }

        return redirect()->back()->with([
            'unsuccess' => 'System error please try again'
        ]);
    }


    public function changeDropOffSave(Request $request)
    {
        $student = Student::find($request->student_id);

        $student->lat_drop = $request->lat;
        $student->lng_drop = $request->lng;
      

        if ($student->update()) {
            return redirect()->route('childernlistpickdrop')->with([
                'success' => 'Pickup/Drop off changed'
            ]);
        }

        return redirect()->back()->with([
            'unsuccess' => 'System error please try again'
        ]);
    }

    public function getVehicle()
    {
        

        $parent = Auth::user();

        $students = Student::where('parent_id', '=', $parent->id)->get();

        $pNofitications = User::find($parent->id)->unreadNotifications;

        $numOfNotifications = count($pNofitications);

        return view('parentlogin.driverdetails')->with([
            'students' => $students,
            'numOfNotifications' => $numOfNotifications
        ]);
    }

    public function getAttendanceAbsent()
    {
        $parent = Auth::user();

        $students = Student::where('parent_id', '=', $parent->id)->get();

        $array = [];

        foreach ($students as $student) {

            $attendances = Attendance::where('student_id', '=', $student->id)->where('present', 'LIKE', 'false')->get();
            
           

            foreach ($attendances as $attendance) {
                $flag = FlagOff::where('student_id', '=', $student->id)
                                     ->where('date', '=',$attendance->date)
                                     ->first();
                
            }
            
        }
    }

    public function schoolEvents()
    {
        $parent = Auth::user();

        $schoolterm = SchoolTermDate::where('status', '=', 1)->first();

        $terms = TermEvent::where('term_id', '=', $schoolterm->id)->get();

        $pNofitications = User::find($parent->id)->unreadNotifications;

        $numOfNotifications = count($pNofitications);

        return view('parentlogin.term_events')->with([
            'terms' => $terms,
            'numOfNotifications' => $numOfNotifications
        ]);
    }

    public function schoolHolidays()
    {
        $term = SchoolTermDate::where('status', '=', 1)->first();

        if (!$term) {
            return redirect()->back()->with('unsuccess', 'Terms have not been created');
        }

        $terms = TermHoliday::where('term_id', '=', $term->id)->get();

        $user = Auth::user();
        
        if ($user->user_type == 'parent') {
            $pNofitications = User::find($user->id)->unreadNotifications;

            $numOfNotifications = count($pNofitications);


            return view('parentlogin.school_holiday')->with([
                'terms' => $terms,
                'numOfNotifications' => $numOfNotifications
            ]);
        }
    }

    /***
     * send app links
     */
    public function sendAppLinks($id) {
        $user = User::find(Crypt::decrypt($id));

        $app_links = DB::table('app_links')->find(1);
        if (! $app_links) {
            return redirect()->back()->with('unsuccess','No links found. Kindly add in settings');
        }

        $generator = new ComputerPasswordGenerator();
        $generator->setLowercase()->setNumbers(false)->setSymbols(false)->setLength(6);
        $password = $generator->generatePassword();

        $user->password = Hash::make($password);
        $user->update();

        Notification::send($user, new ShareAppLinkNotification($password));
        //Notification::send($user, new GeneratedPassword($password));

        return redirect()->back()->with('success','Notification sent');
    }
}
