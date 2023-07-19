<?php

use App\Events\SchoolTripVehicle;
use App\Http\Controllers\BusMaintenanceController;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\SchoolFees;
use App\Models\SchoolTermDate;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Pusher\PushNotifications\PushNotifications;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//-1.2802458785936248, 36.844940853243145
// -1.2803960447960747, 36.845498752743254 -pumu
//-1.2802026910800242, 36.9714821085605
//-1.2903062018525262, 36.87736353280247 buru
//-1.2912452792160183, 36.86530454389917 jeri

//-1.2795252771904817, 36.87858976808946 
//-1.2789031597975657, 36.87435187765599


//-1.2791653992490908, 36.87437421652472
Route::get('/', function () {
    return redirect('/login');
});


Route::get('/school-trip-tracking', function () {
    event(new SchoolTripVehicle('-1.2903062018525262', '36.87736353280247', 1, 50, 60));
});

Route::get('/get-encypted', function() {
    $private = "-----BEGIN RSA PRIVATE KEY-----
MIICWwIBAAKBgQCzoN1rGpG4oIam1m2fup1ruY5enRGxF9KJtnhc2XZZoTn2mRz+
oqFJEvgN0DsfNrjpAJRModM9qHFx4u2wEZgSjHvI2IgVp0t5R2Ji/v3bwwcYKy9M
UhL6Qp24EYyi6awh8uK8BovNCM7IzWFOgBxTtOJ8oBUkko01QfIIG+uoAQIDAQAB
An8l48jQzsnuJ+4/QvvctYB/OKTPUFJrCJtgcRzyeOx9+4Q+gA2dqLBcuaOZRlMy
Qli+zWB6yafFWcKUQ0nf2dY5t86wubsSAaHrSMDCASjLIJJeVDEqPe+Gj+w3RAXw
vb8MW4l7I9T3sSRukn0CnIhGU0KT8+znTHQrAvxNFFbZAkEA+yyTC2FSEGrGqKEx
Vao0ZBegnyoWIN26Xyh+i0c1mZKYHNw363NbMIo3VLQRrnQ08OzXNXE4pxKH+ACN
s1wAjwJBALcUYq619D42YmwpSoPLIUWAFHZmbQYQbO+N+wBlopP0nE6CimC5HsTI
uMAqefnAXRIEU9CM5h3u+6zFVCyi9m8CQQD4JXqEtLppw8POl6nw8z3dYUZr2R2R
jN1y48PZgBmhRqYHZT3N3OLLmtG9WkVZsC8ZkzOu9dO9o943EvzrpUpbAkEAliv9
iiusDX/Umb4A5jwvrW+S2U/I6+l7QcBne/riMZS6xddkJFSUvXubt9zfspIshYPR
MEby1ujZve0az4ZYtwJAa00wn3MncsMiYkwmPIqIruAT5AMkTHLGhddaEFmuQ/kP
xrVrCDQlcV53PNeRoldVb2YSXu58gMeI/SOQIgKMzw==
-----END RSA PRIVATE KEY-----";

    $public = "-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCzoN1rGpG4oIam1m2fup1ruY5e
nRGxF9KJtnhc2XZZoTn2mRz+oqFJEvgN0DsfNrjpAJRModM9qHFx4u2wEZgSjHvI
2IgVp0t5R2Ji/v3bwwcYKy9MUhL6Qp24EYyi6awh8uK8BovNCM7IzWFOgBxTtOJ8
oBUkko01QfIIG+uoAQIDAQAB
-----END PUBLIC KEY-----";


    $data = "X/SctWYJ+4HYpKGbcRMC3V88Y3TjQAY0g3tdwUQPeujPsQVPNHnnoxK/d4sEgsVHNVi+gXR3Z6IDF1Xjbdhk8/KEHjI/BSL2mGA3citEcEjgwWsKG4qrRGXWW3S0UR6/MccOliWZonQrNtbrESITNccJL9O1gZAD3WOG1p8xhik=";
    
    openssl_private_decrypt(base64_decode($data), $decrypted, $private);

    var_dump($decrypted);
});

Route::get('/test-firebase', function() {
    $url = 'https://us-central1-mfika-59523.cloudfunctions.net/api/parents/create';

        // Data to send in the request
        $data = [
            "guid" => 10,
            "first_name" => 'fika',
            "email" => "fika@gmail.com",
            "password" => "12345678",
            "mobile" => "+254715100539",
            "national_id_number" => "2027",
            "presence" => true
        ];
        
        $headers = [
            'Accept' => '*',
            'Connection' => 'keep-alive'
        ];
        // Initialize cURL
        /*
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
        */
        $response = Http::withHeaders($headers)->post($url, $data);
        dd($response);
});

Route::get('/test-tracker', function() {
    $term = SchoolTermDate::where('status','=',1)->first();
        if (! $term) {
            return  redirect()->route('term.create')->with('unsuccess','Please add term');
        }

        $schoolFees = SchoolFees::where('term','=', $term->id)->get();
    return view('test-tracker', compact('schoolFees','term'));
});




Route::group(['middleware' => ['auth','is_disabled','settings', 'email_settings','payment_settings','notification_settings','app_links','terminology','isActiveTerm','is_attendant','is_driver','is_teacher', 'is_director', 'SmsSettings']], function () {
    Route::get('/mail-tracker', function () {
        $app_links = DB::table('app_links')->find(1);
        $password = 12352465;
        return (new MailMessage())->markdown('applinks',['url' => $app_links,'password' => $password]);
        
    });

    Route::get('/pusher-beams-js','HomeController@pusherBeams');
    Route::get('/test-beams-js',function(){
        $user = 2;
        
        $pushNotifications = new PushNotifications([
            "instanceId" => "c880bb01-d93f-4eb8-9fd1-0a3003477735",
            "secretKey" => "57C522657EAD4C0D6DE3D72A4368DF5F0BA256AC4B0AE610714A85CC195DB17F",
        ]);
        $publishResponse = $pushNotifications->publishToInterests(
            ['transport-'.$user],
            [
                "fcm" => [
                    "notification" => [
                        "title" => "Start Trip",
                        "body" => "vehicle commenced",
                        "icon" => "https://cdn-icons-png.flaticon.com/512/9875/9875255.png",
                    ],
                ],
                "web" => [
                    "time_to_live" => 3600,
                    "notification" => [
                        "title" => "Start Trip",
                        "body" => "vehicle commenced",
                        "icon" => "https://cdn-icons-png.flaticon.com/512/9875/9875255.png",
                        "deep_link" => url('/notification/seenotify'), //url to take user when clicked the notification
                        "hide_notification_if_site_has_focus" => true
                    ]
                ]
            ]
        );

        return true;
    });
    Route::get('/service-worker.js', function () {
        return response()->file(public_path('service-worker.js'));
    });

    Route::post('/stand-in-vehicle','VehicleController@standInVehicle')->name('stand_in_vehicle');
    Route::post('/stand-in-driver','StaffController@standInDriver')->name('stand_in_driver');
    Route::post('/stand-in-attendant','StaffController@standInAttendant')->name('stand_in_attendant');
    
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware(['auth']);
    Route::get('/home-alerts','HomeController@systemWarnings');

    //Home data
    Route::get('/home-first-top-tabs', 'HomeController@firstTopTab');


    Route::get('/get-zones-for-routes/{id}','RouteController@getZone');

    Route::get('/test-vehicle-location', 'VehicleController@fireEvent');
    
    Route::resource('drivers', 'DriverController');
    Route::get('/maintenance', 'BusMaintenanceController@index')->name('maintenance_index');
    Route::get('/maintenance/{id}', 'BusMaintenanceController@show')->name('maintenance_show');
   
    Route::get('/warranty/claims/{id}','WarrantyController@claims')->name('warranty-claims');
   
    
    Route::post('/caused-by','IncidentController@getCausedBy');
    Route::get('/get-trips-incident/{id}','IncidentController@getVehicleTrips');
    Route::get('/incident/images/{id}','IncidentController@incidentImages');
    Route::resource('incidents','IncidentController');
    
    Route::post('/offence-activate','OffenceController@activate')->name('activate_offence');
    Route::resource('offence','OffenceController');
    Route::get('/claims/create/{id}','InsuranceClaimsController@create')->name('claims.create');
    Route::get('/claim-download-report/{id}', 'InsuranceClaimsController@downloadReport')->name('dwn_report');
    Route::get('/claim-download-statement/{id}', 'InsuranceClaimsController@downloadStatement')->name('dwn_statement');
    Route::resource('claims','InsuranceClaimsController')->except('create');
    
    Route::post('/get-offence-user','OffenceController@getUser')->name('offence_getuser');
    Route::resource('flag', 'FlagOffController');
    
    Route::post('/event-activate','TermEventController@activate')->name('activate_event');
    Route::resource('term_events', 'TermEventController');
    Route::resource('classes', 'StudentClassController');
    //Route::resource('streams', 'StreamController');
    Route::resource('groups', 'ClassGroupController');
    Route::resource('reviews', 'ReviewController')->except('show');
    Route::post('/disable-insurace/{id}', 'InsuranceController@disableInsurance')->name('disable-insurace');
    
    Route::get('/geo-fence', 'GeofenceController@create')->name('geofence_create');
    Route::get('/geo-fence-add/{id}', 'GeofenceController@add')->name('geofence_add');
    Route::get('/show-geo-fence/{id}', 'GeofenceController@index')->name('geofence_show');
    Route::post('/store-geo-fence', 'GeofenceController@store')->name('geofence_store');
    Route::get('/edit-geo-fence/{id}', 'GeofenceController@edit')->name('geofence_edit');
    Route::post('/update-geo-fence/{id}', 'GeofenceController@update')->name('geofence_update');

    //dash data
    Route::get('/parent-std-loc','HomeController@parentsChildLocationProvided');

   

    //school trips route path
    Route::get('/schooltrips/schooltriproute/{id}', 'SchoolTripController@schooltriproute')->name('schooltriproute');
    Route::get('/driver-myschooltrips/schooltrips/schooltripshow/{id}', 'SchoolTripController@showRoutePath')->name('showroutepath');
    Route::post('/schooltrips/schooltriproute/save', 'SchoolTripController@saveRoutePath')->name('saveroutepath');
    Route::post('/schooltrips/addstudents/save', 'SchoolTripController@addStudents')->name('addstudents');
    //school trip route with more than one destination
    Route::get('/schooltrips/triproute/{id}', 'SchoolTripController@schoolTripRouteMoreDests')->name('schoolTripRouteMoreDests');

    //school trip edit
    Route::get('/school-trip/{id}', 'SchoolTripController@editNoWayPoints')->name('editpage_no_wayponts');
    Route::get('/school-trip-waypoints/{id}', 'SchoolTripController@editWayPoints')->name('editpage_wayponts');

    
    //student change pick up if yes or no
    Route::post('/change-pickup/{id}', 'StudentController@puckUp')->name('pick_up');

    Route::get('/vehicle/trips/{id}', 'TripController@getVehicleTrip')->name('get_vehicle_trips');
    Route::post('/vehicle/trips/edit', 'TripController@getVehicleTripEdit')->name('getVehicleTripEdit');
    Route::get('/addstd/vehicle/trips/{id}', 'ParentsController@getVehicleTrip')->name('getstd_vehicle_trips');


    Route::get('/allstds', 'StudentController@allstd')->name('allstds');

   
    //parent login
    /*
    Route::get('/phome', 'ParentsController@phome')->name('phome');
    Route::get('/home-data', 'ParentsController@getHomeData')->name('home_data');
    Route::post('/getlatlong', 'ParentsController@getLangLong')->name('getlatlong');
    Route::get('/children/{id}', 'ParentsController@myChildren')->name('pchildren');
    Route::get('/pinvoices/paid/{id}', 'ParentsController@paidInv')->name('pinvoice');
    Route::get('/unpinvoices/unpaid/{id}', 'ParentsController@unpaidInv')->name('unpinvoice');
    Route::get('/addchildview/{id}', 'ParentsController@addChildView')->name('addchildview');
    Route::post('/addchild', 'ParentsController@addChild')->name('addchild');
    Route::get('/pattendance/{id}', 'ParentsController@attendanceView')->name('attendance_view');
    Route::get('/pattendance/data/{id}', 'ParentsController@getAttendanceData')->name('attendance_data');
    Route::get('/pattendance-school/data/{id}', 'ParentsController@getAttendanceDataSchool')->name('attendanceschool_data');
    Route::get('/trips-parent', 'ParentsController@getSchooltrips')->name('parent_gettrips');
    Route::post('/pcheckouttrip', 'CheckoutController@pCheckoutTrip')->name('pcheckouttrip'); //school tip checkout
    Route::post('/checkout-trip', 'PCheckoutController@store')->name('checkout-trips');  //school trip checkout
    Route::get('/chargeupdatetrip/{id}/{student_id}', 'CheckoutController@myupdateTrip')->name('chekout_updatetrip');
    Route::get('/pickup/dropoff', 'ParentsController@childernListPickDrop')->name('childernlistpickdrop');
    Route::get('/pickup/dropoff/confirm/{id}', 'ParentsController@confirmpage')->name('confirmpage');
    Route::post('/pickup/dropoff/confirmed', 'ParentsController@confirmedPickup')->name('confirmedpickup');
    Route::get('/mstudt/vehicle','ParentsController@getVehicle')->name('getvehilce');
    Route::get('/pickup/dropoff/change/{id}', 'ParentsController@changePickupView')->name('changepickuppage');
    Route::post('/pickup/dropoff/change/save', 'ParentsController@changePickupSave')->name('changepickupsave');
    */
    //pickup routes
    Route::get('/pickup/dropoff/select/{id}', 'ParentsController@selectPickupView')->name('selectpickuppage');
    Route::post('/pickup/dropoff/select/save', 'ParentsController@selectPickupSave')->name('selectpickupsave');
    //drop off routes
    Route::get('/dropoff/select/{id}', 'ParentsController@changeDropOffView')->name('changeDropOffView');
    Route::post('/dropoff/select/save', 'ParentsController@changeDropOffSave')->name('changeDropOffSave');


    Route::get('/term-events', 'ParentsController@schoolEvents')->name('schoolevents');
    Route::get('/term-holidays', 'ParentsController@schoolHolidays')->name('schoolholidays');

   

    //vehicle tracker
    Route::get('/tracker/{id}', 'TrackerController@index')->name('tracker');
    Route::get('/allvehicles/{id}', 'TrackerController@allVehicles')->name('all_vehicles');
    Route::post('/getvehicle','TrackerController@getVehicle')->name('get_vehicle');
    Route::post('/vehicleoutofzone', 'VehicleController@outOfFence')->name('vehicleoutofzone');
    Route::get('/students-in-trip/{id}','TrackerController@getTripStudentd')->name('getTripStudentd');
    Route::get('/edit-fence/{id}', 'VehicleController@editFence')->name('edit_fence');
    Route::patch('/update-fence','VehicleController@updateFence')->name('update_fence');
    //parent

    Route::get('/pnotification/pseenotify', 'ParentsController@getNotification')->name('pnotification_get');
    Route::get('/markasread/{id}', 'ParentsController@markAsRead')->name('pnotification_read');
    Route::post('/pcheckout', 'CheckoutController@index')->name('pcheckout');
    Route::post('/checkout', 'CheckoutController@store')->name('checkout');
    Route::get('/chargeupdate/{id}', 'CheckoutController@myupdate')->name('chekout_update');

    //driver
    Route::get('/mystudents', 'DriverController@myStudents')->name('driver_mystudents');

    //change pass
    Route::get('/change', 'HomeController@changepass')->name('changepass');
    Route::post('/changePassword','HomeController@changePassword')->name('changePassword');

    //api
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/token', 'HomeController@personalToken');

    

    //payment gateway
    Route::get('/get-key', 'CheckoutController@getKey')->name('getkey');
    Route::get('/get-key-two', 'CheckoutController@getKeytwo')->name('getkeytwo');
    Route::post('/paygate-store', 'PaymentGatewaySettingController@store')->name('paygate-store');


    //reports 
    Route::get('/attendance-report', 'ReportsController@attendance')->name('att-report');
    Route::get('/attendance-report-table', 'ReportsController@getAttendance')->name('attendance-report-table');
    Route::post('/attendance-report-query', 'ReportsController@getAttendanceQuery')->name('attendance-report-query');

    //financial reports
    Route::get('/finance-report', 'ReportsController@financialView')->name('financialview');
    Route::get('/finance-report-data', 'ReportsController@getFinancial')->name('financialdata');
    Route::post('/finance-report-query', 'ReportsController@queryFinancial')->name('financialquery');

    Route::get('/mainte-image/{id}','BusMaintenanceController@getImages')->name('getImages');

    /*
    Route::get('/roles', function(){
        $user = User::find(1);

        $permissions = Permission::all();

        //give permissions by id
        //$user->permissions()->sync([1, 2, 5]);

        foreach ($user->permissions as $item) {
            var_dump($item->name);
        }

        
    });*/


    //home logic
    Route::get('/header-data', 'HomeController@headerData')->name('header-data');
    Route::get('/chart-data', 'HomeController@chartData')->name('chart-data');
    Route::get('/officestaff-data', 'HomeController@officeStaff')->name('officestaff-data');
    Route::get('/vehicle-staff-num', 'HomeController@vehicleStaffNum')->name('vehiclestaffnum');


    

    //routes maps
    Route::get('/polyline', function () {
        $user = Auth::user();

            $notifications = User::find($user->id)->unreadNotifications;
        return view('routes.polyline')->with('notifications', $notifications);
    });

    Route::get('/polyline/{id}', 'RoutePolylineController@index')->name('polyline');
    Route::get('/polyline-add/{id}', 'RoutePolylineController@create')->name('polyline_create');

    Route::get('/polyline-edit/{id}', 'RoutePolylineController@edit')->name('polyline_edit');
    Route::patch('/polyline-update/{id}', 'RoutePolylineController@update')->name('polyline_update');

    Route::post('/save-route-path/{id}', 'RoutePolylineController@store')->name('save-route-path');

    Route::post('/polyline-delete', 'RoutePolylineController@destroy')->name('polyline-delete');

    //profile settings
    Route::get('/profile/{id}/show','ProfileController@showPage')->name('profile_page');
    Route::get('/profile/{id}', 'ProfileController@show')->name('profile_show');
    Route::post('/profile/update/{id}', 'ProfileController@update')->name('profile_update');


    


    //driver login and data
    
    Route::get('/home-driver', 'DriverLoginContoller@index')->name('driverlogin_home');
    Route::get('/bus/route/{id}', 'DriverLoginContoller@getRoutePath')->name('driverlogin_getroutepath');
    Route::get('/my-students/confirm-pickup/{id}', 'p@confirmPuckupPage')->name('driverlogin_confirmpickup');
    Route::get('/my-students', 'DriverLoginContoller@myStudents')->name('driverlogin_mystudents');
    Route::get('/driver-notifcations', 'DriverLoginContoller@notificationList')->name('driverlogin_notif');
    Route::post('/driver/attendance/','DriverLoginContoller@getStudents')->name('driverlogin_students');
    Route::post('/driver/attendance/store','DriverLoginContoller@store')->name('driverlogin_attendancestore');
    Route::post('/confirm-pickup/save','DriverLoginContoller@saveConfirmation')->name('driverlogin_saveconfirmation');
    Route::post('/notify-here', 'DriverLoginContoller@notifyHere')->name('driverlogin_notifyhere');
    Route::get('/notify-late', 'DriverLoginContoller@sendLateNotification')->name('driverlogin_notifylate');
    Route::get('/notify-stop', 'DriverLoginContoller@notifyStop')->name('driverlogin_notifystop');
    Route::get('/notify-start', 'DriverLoginContoller@notifyStart')->name('driverlogin_notifystart');

    Route::get('/driver-myschooltrips', 'DriverLoginContoller@mySchoolTrips')->name('driverlogin_myschooltrips');
    Route::post('/driverschooltrips', 'DriverLoginContoller@saveApproval')->name('save_approval');
    Route::post('/driverschooltrip/start', 'DriverLoginContoller@sendStartSchoolTrips')->name('send_start_notification');

    Route::get('/dterm-events', 'DriverLoginContoller@schoolEvents')->name('schooldevents');
    Route::get('/dterm-holidays', 'DriverLoginContoller@schoolHolidays')->name('schooldholidays');

    Route::get('/drivertrips/reacheddestination/{id}', 'DriverLoginContoller@sendReachedDestination')->name('sendReachedDestination');
    Route::get('/drivertrips/goingback/{id}', 'DriverLoginContoller@sendGoindBack')->name('sendGoindBack');
    Route::get('/drivertrips/sendReachedSchool/{id}', 'DriverLoginContoller@sendReachedSchool')->name('sendReachedSchool');
    
    Route::post('/location-update', 'DriverLoginContoller@saveCoords')->name('saveCoords');


    // send whatsapp msg event
    Route::get('/sms-whatsapp/{id}', 'TermEventController@sendMesgWhatsapp')->name('send_whatsapp');

    //show reviews
    Route::get('/reviews/show', 'ReviewController@show')->name('review_show');

    

    Route::get('/chatif-redirect', 'WhatsappSettingController@chatifRedirect')->name('chatif_redirect');

    //BUS ATTENDANT
    Route::resource('bus-attendant','BusAttendantController');


    //vehicle student allocation
    Route::get('/allocation/{id}', 'AllocationController@create')->name('allocation_create');
    //allocation check if student has been allocated am
    Route::get('/check-allocation/{id}', 'AllocationController@chckIfAllocatedAm')->name('check_allocation');
    //save allocation pickup
    Route::post('/allocation-save','AllocationController@store')->name('allocation_save');
    //save allocation dropoff
    Route::post('/allocation-save-dropoff','AllocationController@storeDropOff')->name('allocation_save_dropoff');
    //Allocation get zones
    Route::get('/get-zones', 'AllocationController@zones')->name('get_zone_geofences');
    //allocation get zone routes
    Route::get('/get-zone-routes/{id}', 'AllocationController@getZoneRoutes')->name('get_zone_routes');
    //used in allocation for getting parent details
    Route::get('/allocation/find-student/{id}','AllocationController@getStudent')->name('allocation_get_student');
    //get vehicle 
    Route::post('/get-vehicle', 'AllocationController@getVehicle')->name('get_vehicle_allocation');
    //get vehicle trips
    Route::post('/get-vehicle-trips', 'AllocationController@getVehicleTrip')->name('get_vehicletrips_allocation');
   

    //allocation dropoff
    Route::get('/allocation-dropoff/{id}', 'AllocationController@createDropOff')->name('allocation_create_dropoff');
    //student transport definition
    Route::post('/student-transport-definition/{id}','StudentController@saveTripDefinition')->name('saveTripDefinition');

    //get streams
    Route::get('/get-streams/{id}','StudentController@getStreams')->name('get_streams');

    //get zone geofence page
    Route::get('/zone/geofence/{id}','ZoneController@zoneGeoFencePage')->name('zoneGeoFencePage');
    Route::get('/zone/coordinates/{id}','ZoneController@getZoneGeoFenceCoords')->name('getZoneGeoFenceCoords');

    Route::get('/zone/edit-geofence/{id}','ZoneController@zoneGeoFenceEdit')->name('zoneGeoFenceEdit');
    Route::post('/zone/update-geofence/{id}','ZoneController@updateZoneGeoFence')->name('updateZoneGeoFence');
    Route::get('/zone-all-fences','ZoneController@zones');

    //school trips
    Route::get('/schooltripst/showmytrips/show/{id}', 'TeacherContoller@show')->name('teachertrips_show');
    Route::get('/schooltripst/attendance/{id}', 'SchoolTripController@markAttendance')->name('teachertrips_markattendance');
    Route::post('/schooltripst/attendance/save', 'TeacherContoller@saveAttendance')->name('teachertrips_saveattendance');
    Route::get('/schooltripst/return-attendance/{id}', 'TeacherContoller@markAttendanceReturn')->name('teachertrips_markattendancereturn');
    Route::post('/schooltripst/return-attendance/save', 'TeacherContoller@saveAttendanceReturn')->name('teachertrips_saveattendancereturn'); //save depature 
    Route::get('/schooltripst/add-students/{id}','TeacherContoller@pageToAddStdToScholTrip')->name('add_std_to_schooltrip');
    Route::post('/schooltripst/students-add', 'TeacherContoller@addMyStudents')->name('teachertrips_addmystudents');
    Route::post('/schooltripst/students-remove', 'TeacherContoller@removeStudent')->name('schooltrip_remove_student');
    Route::post('/schooltrip-activate','SchoolTripController@activate')->name('activate_schooltrip');
    Route::resource('schooltrips', 'SchoolTripController');
    Route::get('/attendances/list','AttendanceController@index')->name('attr_list');
    Route::resource('attendances', 'AttendanceController')->except('index');

    

    //paypal
    Route::get('/paypal','api\v1\SchoolFeesController@paypalPage');
    Route::get('/paypal-token','api\v1\SchoolFeesController@generateToken')->name('paypal_token');
    Route::post('/paypal-order','api\v1\SchoolFeesController@createOrder')->name('paypal_order');
    Route::post('/testing-json','api\v1\SchoolFeesController@test');
    Route::post('/capture-paypal-order','api\v1\SchoolFeesController@capturePayment')->name('capture-paypal-order');

    //store fee payment
    Route::get('/school-fees-payment/{id}','SchoolFeesController@paymentPage')->name('payment_page');
    Route::post('/school-fees-payment','SchoolFeesController@storePayment')->name('store_payment');

    //save inspection report 
    Route::get('/inspection-report/download/{id}', 'InspectionController@downloadReport')->name('inspection-report-download');
    Route::post('/inspection-report', 'InspectionController@saveInspectionReport')->name('inspection-report');
    Route::get('/share-app-link/{id}','ParentsController@sendAppLinks')->name('send_app_links');
});

// Reports
Route::get('/trip-report','TripReportController@index')->name('trip_report');
Route::get('/trip-report/{id}','TripReportController@show')->name('trip_report_show');
Route::get('/compliance-report/{id}','TripReportController@complianceReport')->name('compliance_report');

Route::group(['middleware' => ['parents','is_disabled','settings','whatsapp_settings','email_settings', 'app_links','payment_settings','notification_settings','terminology','staff','license','isActiveTerm','TermHoliday','Grades','TermSchoolFees','Garage','vehicle','Compliance','Maintenance','SmsSettings']], function() {
    Route::post('/student-activate','StudentController@activate')->name('student_activate');
    Route::resource('students', 'StudentController');
});

Route::group(['middleware' => ['settings','is_disabled','whatsapp_settings','email_settings', 'app_links','payment_settings','notification_settings','terminology','staff','license','isActiveTerm','TermHoliday','Grades','TermSchoolFees','Garage','vehicle','Compliance','Maintenance','SmsSettings']], function() {
    Route::post('/parent-activate','ParentsController@activateParent')->name('parent_activate');
    Route::resource('parents', 'ParentsController');
});

Route::group(['middleware' => ['Compliance','is_disabled','settings','whatsapp_settings','email_settings', 'app_links','payment_settings','notification_settings','terminology','staff','license','isActiveTerm','TermHoliday','Grades','TermSchoolFees','Garage','vehicle','SmsSettings']], function(){
    Route::post('/warranty-activate','WarrantyController@activate')->name('warranty_activate');
    Route::get('/warranty/create/{id}','WarrantyController@create')->name('warranty.create');
    Route::resource('warranty', 'WarrantyController')->except('create');
});

Route::group(['middleware' => ['vehicle','is_disabled','settings','whatsapp_settings','email_settings', 'app_links','payment_settings','notification_settings','terminology','staff','license','isActiveTerm','TermHoliday','Grades','TermSchoolFees','Garage','SmsSettings']], function() {
    Route::post('/insurance/activate', 'InsuranceController@activate')->name('insurance_activate');
    Route::post('/insurance-renew','InsuranceController@renew')->name('renew_insurance');
    Route::post('/activate-inspection','InspectionController@activate')->name('activate_inspection');
    Route::get('/insurance/create/{id}','InsuranceController@create')->name('create_ins');
    Route::resource('insurance','InsuranceController')->except('create');
    Route::get('/inspection/create/{id}','InspectionController@create')->name('inspection.create');
    Route::resource('inspection', 'InspectionController')->except('create');
});

Route::group(['middleware' => ['settings','is_disabled','whatsapp_settings','email_settings', 'app_links','payment_settings','notification_settings','terminology','staff','license','isActiveTerm','TermHoliday','Grades','TermSchoolFees','Garage','SmsSettings']], function() {
    Route::post('/activate-vehicle','VehicleController@activate')->name('activate_vehicle');
    Route::post('/route-activate','RouteController@activate')->name('activate_route');
    Route::post('/zone-activate','ZoneController@activate')->name('activate_zone');
    Route::resource('vehicles', 'VehicleController');
    Route::resource('routes', 'RouteController');
    Route::resource('zones', 'ZoneController');
    Route::get('/trips/create/{id}', 'TripController@myCreate')->name('trips_create');
    Route::resource('trips', 'TripController')->except('create');
});


Route::group(['middleware' => ['settings','is_disabled','whatsapp_settings','email_settings', 'app_links','payment_settings','notification_settings','terminology','staff','license','isActiveTerm','TermHoliday','Grades','TermSchoolFees','SmsSettings']], function(){
    //GARAGE ROUTES
    Route::post('/activate-garage','GarageController@activate')->name('activate_garage');
    Route::resource('garage','GarageController');
});

Route::group(['middleware' => ['settings','is_disabled','whatsapp_settings','email_settings', 'app_links','payment_settings','notification_settings','terminology','staff','license','isActiveTerm','TermHoliday','Grades','SmsSettings']], function(){
    Route::post('/school-fees-activate', 'SchoolFeesController@activate')->name('activate_schoolfees');
    Route::get('/school-fees/create','SchoolFeesController@create')->name('create_school_fees');
    Route::post('/school-fees-assign', 'SchoolFeesController@assignFee')->name('assignFee');
    Route::resource('school-fees','SchoolFeesController')->except('create');
});


Route::group(['middleware' => ['settings','is_disabled','whatsapp_settings','email_settings', 'app_links','payment_settings','notification_settings','terminology','staff','license','isActiveTerm','TermHoliday','SmsSettings']], function() {
    //GROUP GRADE STREAMS
    Route::get('/grades','GradeController@index')->name('grades_page');
    Route::get('/group/view','GradeController@groupCreatePage')->name('group_view');
    Route::post('/group/store','GradeController@groupStore')->name('group_store');
    Route::get('/grade/view','GradeController@gradeCreatePage')->name('grade_view');
    Route::post('/grade/store','GradeController@gradeStore')->name('grade_store');
    Route::get('/stream/view','GradeController@streamCreatePage')->name('stream_view');
    Route::post('/stream/store','GradeController@streamStore')->name('stream_store');
    Route::get('/stream-teachers','GradeController@getTeachers');

    //GROUP GRADE STREAMS EDITS
    Route::get('/group/view/edit/{id}','GradeController@editGroupPage')->name('editGroupPage');
    Route::post('/group/update','GradeController@groupUpdateStore')->name('groupUpdateStore');
    Route::get('/grade/view/edit/{id}','GradeController@editGradePage')->name('editGradePage');
    Route::post('/grade/update','GradeController@gradeUpdateStore')->name('gradeUpdateStore');
    Route::get('/stream/view/edit/{id}','GradeController@streamEditPage')->name('streamEditPage');
    Route::post('/stream/update','GradeController@streamUpdateStore')->name('streamUpdateStore');

    //GROUP GRADE STREAMS deactivate
    Route::post('/group/delete/{id}','GradeController@deleteGroup')->name('deleteGroup');
    Route::post('/grade/delete/{id}','GradeController@deleteGrade')->name('deleteGrade');
    Route::post('/stream/delete/{id}','GradeController@deleteStream')->name('deleteStream');

    //activate
    Route::post('/group-activate','GradeController@activateGroup')->name('activateGroup');
    Route::post('/grade-activate','GradeController@activateGrade')->name('activateGrade');
    Route::post('/stream-activate','GradeController@activateStream')->name('activateStream');

});

//term holiday requires school term 
Route::group(['middlware' => ['settings','is_disabled','whatsapp_settings','email_settings', 'app_links','payment_settings','notification_settings','terminology','staff','license','isActiveTerm','SmsSettings']], function() {
    Route::post('/termholiday-activate','TermHolidayController@activate')->name('activate_holiday');
    Route::resource('term_holiday', 'TermHolidayController');
});

// term required licence
Route::group(['middleware' => ['settings','is_disabled','whatsapp_settings','email_settings', 'app_links','payment_settings','notification_settings','terminology','staff','license','SmsSettings']], function(){
    Route::resource('term', 'SchoolTermDateController')->middleware('auth');
    Route::post('/terms/active', 'SchoolTermDateController@activateTerm')->name('activate_term')->middleware('auth');
});

//license
Route::group(['middleware' => ['staff','settings','is_disabled','whatsapp_settings','email_settings', 'app_links','payment_settings','notification_settings','terminology', 'SmsSettings']], function () {
    Route::get('/license/create/{id}','DriverLicenceController@create')->name('create_dl');
    Route::post('/license/renew','DriverLicenceController@renew')->name('dl_renew');
    Route::resource('license', 'DriverLicenceController')->except('create');
});

//staff routes
Route::group(['middleware' => ['settings','is_disabled','whatsapp_settings','email_settings', 'app_links','payment_settings','notification_settings','terminology','SmsSettings']], function() {
    //staff
    Route::post('/staff-activate','StaffController@activate')->name('activate_staff');
    Route::get('/staff', 'StaffController@index')->name('staff_index');
    Route::get('/staff/create', 'StaffController@create')->name('staff_create');
    Route::post('/staff/store', 'StaffController@store')->name('staff_store');
    Route::get('/staff/edit/{id}', 'StaffController@edit')->name('staff_edit');
    Route::patch('/staff/update/{id}', 'StaffController@update')->name('staff_update');
    Route::delete('/staff/delete/{id}', 'StaffController@destroy')->name('staff_destroy');
    Route::get('/notification/pnotif', 'StaffController@notificationView')->name('pnotification_view');
    Route::post('/notification/pnotify/send', 'StaffController@sendNotification')->name('pnotification_send');
    Route::get('/notification/markasread/staff/{id}', 'StaffController@markAsRead')->name('notification_read');
    Route::get('/notification/seenotify', 'StaffController@getNotification')->name('notification_get');
    Route::get('/notification/all', 'AllNotificationsController@index')->name('all_notications');
    Route::post('/schooltrips/approve/route', 'StaffController@approveRoute')->name('schooltrip_approve');
    Route::get('/attendances/absent-today', 'StaffController@adbsentToday')->name('absenttodaystd');
    Route::get('/delete_user_notifications','StaffController@markAll')->name('delete_user_notifications');
    //vaildate email
    Route::post('/validate-email','StaffController@validateEmail');
    Route::post('/validate-stnum','StaffController@validateStaffNumber');
    Route::post('/validate-idnum','StaffController@validateIDNumber');
    Route::post('/validate-phone','StaffController@validatePhone');
    Route::post('/validate-parent-email','StaffController@validateparentEmail');

});



Route::group(['middleware' => ['auth','settings','is_disabled','is_attendant','is_driver','is_teacher','is_office_staff','is_head_teacher']], function () {
    //message settings
    Route::post('/msg-store', 'DefaultMessageSettingController@store')->name('msg-store');
    //change term active status
    
});

Route::group(['middleware' => ['settings','is_disabled','whatsapp_settings','is_attendant','is_driver','is_teacher','is_office_staff','is_head_teacher']], function () {
    //email settings
    Route::post('/settings/email','EmailSettingsController@store')->name('email_store');
});

Route::group(['middleware' => ['settings','is_disabled','whatsapp_settings','email_settings']], function () {
    //app links
    Route::post('/settings/app-links-store','SettingsController@storeAppLinks')->name('store_app_links');
    Route::get('/settings/app-link','SettingsController@getAppLinks')->name('get_app_links');
});

Route::group(['middleware' => ['settings','is_disabled','whatsapp_settings','email_settings','app_links','is_attendant','is_driver','is_teacher','is_office_staff','is_head_teacher']], function () {
    //payment settings
    Route::get('/settings/payment','SettingsController@paymentPage')->name('payment_page');
    Route::post('/settings/store/payment','SettingsController@paymentStore')->name('payment_store');
});

Route::group(['middleware' => ['auth','is_disabled','payment_settings','settings','whatsapp_settings','email_settings','app_links','is_attendant','is_driver','is_teacher','is_office_staff','is_head_teacher']], function () {
    Route::resource('notification-settings', 'NotificationSettingController');
});

Route::group(['middleware' => ['auth','is_disabled']], function() {
    Route::get('/send-sms','SettingsController@testSms');
    Route::get('/sms-settings','SettingsController@smsSettings')->name('sms_settings');
    Route::post('/sms-settings-save', 'SettingsController@smsSettingsSave')->name('sms_settings_save');
});

Route::group(['middleware' => ['auth','is_disabled','payment_settings','settings','whatsapp_settings','email_settings','app_links','notification_settings','is_attendant','is_driver','is_teacher','is_office_staff','is_head_teacher']], function () {
    Route::resource('terminology', 'TerminologyController');
});

Route::group(['middleware' => ['auth','is_disabled','settings','is_attendant','is_driver','is_teacher','is_office_staff','is_head_teacher']], function () {
    Route::get('/settings/create','SettingsController@create')->name('settings_create');
    Route::post('/settings/center-map', 'SettingsController@saveCenterMap')->name('centermap_store');
    Route::post('/settings/whatsapp', 'WhatsappSettingController@store')->name('store_whatsapp');

});


Route::group(['middleware' => ['auth','is_disabled','is_attendant','is_driver','is_teacher','is_office_staff','is_head_teacher']], function () {
    Route::get('/system-settings','SettingsController@createFirstSettings')->name('first_settings');
    //center map settings
    Route::resource('settings', 'SettingsController')->except('create');
});

Route::group(['middleware' => ['auth','is_disabled','teacher_routes']], function () {
    //school attendance
    Route::resource('school-attendance', 'SchoolAttendanceController')->except('create');
    Route::get('/schoolattendance/create','SchoolAttendanceController@create')->name('schoolattcreate');
    Route::get('/school-attendance-data','SchoolAttendanceController@schoolAttendanceData')->name('schoolattendancedata');
    Route::post('/school-attendance-query','SchoolAttendanceController@schoolAttendanceQuery')->name('schoolattendancequery');
    Route::get('/absent/today','SchoolAttendanceController@adsentToday')->name('absenttoday');

    //teacher school trips
    
    Route::get('/schooltripst/showmytrips', 'TeacherContoller@index')->name('teachertrips');
    
    
    Route::get('/event','TeacherContoller@event')->name('event');
});

Route::get('/css-animation', function() {
    return view('unauth');
});


Auth::routes();
