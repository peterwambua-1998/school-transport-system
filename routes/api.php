<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/

//-1.2898278212273218, 36.87923814925509


// SCHOOL DETAILS
Route::get('/school-details', 'api\v1\LoginController@schoolDetails');

//get profile picture
Route::middleware('auth:api')->get('/profile-picture','api\v1\LoginController@profilePicture');

// RESET PASSWOR
Route::post('/get-token','api\v1\ResetPasswordController@sendMailWithToken'); //requires user email
Route::post('/validate-token','api\v1\ResetPasswordController@validateRedirectTwoFactor'); //requires user email
Route::post('/change-password','api\v1\ResetPasswordController@changePassword'); //requires user email
Route::post('/resend-token','api\v1\ResetPasswordController@resend'); //requires user email

Route::post('/login', 'api\v1\LoginController@login');

Route::middleware('auth:api')->post('/logout', 'api\v1\LoginController@logout');

Route::middleware('auth:api')->get('/getuser', 'api\v1\LoginController@getUser');
Route::middleware('auth:api')->get('/gettrips', 'api\v1\LoginController@getTrips');
Route::middleware('auth:api')->get('/getstudents/{trip_id}', 'api\v1\LoginController@getStudents');

Route::middleware('auth:api')->get('/getvehicle', 'api\v1\LoginController@getVehicle');
Route::middleware('auth:api')->get('/numstd', 'api\v1\LoginController@myStudentCount');
Route::middleware('auth:api')->get('/getparent/{id}', 'api\v1\LoginController@getParent');

Route::middleware('auth:api')->post('/maintenance/{id}', 'api\v1\BusMaintenanceController@store');
//UPDATE PROFILE
Route::middleware('auth:api')->post('/change-profile/{id}','api\v1\LoginController@changeProfile');
//History maintenance
Route::middleware('auth:api')->get('/maintenance-history/{id}', 'api\v1\BusMaintenanceController@maintenanceHistory');

//notifications
Route::middleware('auth:api')->post('/saveNotification', 'api\v1\LoginController@saveNotification');

//NIPE STUDENT IDS
Route::middleware('auth:api')->post('/saveLateNotification/{id}', 'api\v1\LoginController@sendLateNotification'); 
Route::middleware('auth:api')->post('/saveStartNotification/{id}', 'api\v1\LoginController@sendStart');
Route::middleware('auth:api')->post('/saveStopNotification/{id}', 'api\v1\LoginController@sendStop');
//NIPE STUDENT IDS
Route::middleware('auth:api')->post('/saveHereNotification/{id}', 'api\v1\LoginController@sendHere');


Route::middleware('auth:api')->post('/attendance/store', 'api\v1\AttendanceController@store');
Route::middleware('auth:api')->post('/coordinates/store', 'api\v1\LoginController@saveCoords');
Route::middleware('api')->get('/validate/token', 'api\v1\LoginController@authenticated');

Route::middleware('auth:api')->get('/marked-attendance/{id}', 'api\v1\LoginController@getMarkedAttendance');

//school trips
Route::middleware('auth:api')->get('/schooltrips/get','api\v1\AttendanceController@mySchoolTrips');
//route trip change
Route::middleware('auth:api')->get('/schooltrips/approval/{id}','api\v1\AttendanceController@saveApproval');
//send route trip start notification
Route::middleware('auth:api')->get('/schooltrips/start-notif/{id}','api\v1\AttendanceController@sendStartSchoolTrips');
// DAILY TRIPS
Route::middleware('auth:api')->get('/trips/{id}', 'api\v1\ParentsController@getTrips');
//VEHICLE ROUTE
Route::middleware('auth:api')->get('/vehicle/route','api\v1\AttendanceController@getRoutePath');
//ALL STUDENTS
Route::middleware('auth:api')->get('/students/all','api\v1\AttendanceController@myStudents');
//save pick up / drop off confirmation
Route::middleware('auth:api')->get('/students/save/pickup-dropoff/{id}','api\v1\AttendanceController@saveConfirmation');
//Bus assigned students information
Route::middleware('auth:api')->get('/students/parent-info','api\v1\AttendanceController@studentParentInfo');
//SCHOOL TRIP NOTIFICATIONS
Route::middleware('auth:api')->get('/notify/schooltrip-start/{id}','api\v1\LoginController@sendStartSchoolTrips');
Route::middleware('auth:api')->get('/notify/schooltrip-reached/{id}','api\v1\LoginController@sendReachedDestination');
Route::middleware('auth:api')->get('/notify/schooltrip-going-back/{id}','api\v1\LoginController@sendGoindBack');
Route::middleware('auth:api')->get('/notify/schooltrip-reached-school/{id}','api\v1\LoginController@sendReachedSchool');
//GARAGE
Route::middleware('auth:api')->get('/garage','api\v1\BusMaintenanceController@activeGarage');
// DEPATURE CHECK LIST
Route::middleware('auth:api')->get('/depature-checklist/{id}','api\v1\AttendanceController@depatureCheckList');
// RETURN CHECKLIST
Route::middleware('auth:api')->get('/return-checklist/{id}','api\v1\AttendanceController@returnCheckList');
//STORE DEPATURE CHECKLIST
Route::middleware('auth:api')->post('/depature-checklist-save/{id}','api\v1\AttendanceController@storeDepatureCheckList');
//STORE RETURN CHECKLIST
Route::middleware('auth:api')->post('/return-checklist-save/{id}','api\v1\AttendanceController@storeReturnCheckList');
//STORE VEHICLE IMAGE
Route::middleware('auth:api')->post('/update-vehicle-image/{id}','api\v1\LoginController@updateVehicleImage');
//SCHOOL TRIP VEHICLE TRACK
Route::middleware('auth:api')->post('/schooltrip-vehicle-location/{id}','api\v1\SchoolTripController@updateLocation');

Route::middleware('auth:api')->get('/student-list/{id}','api\v1\LoginController@allStudents');
Route::middleware('auth:api')->post('/store-pickup-point/{id}','api\v1\LoginController@addPickupPoint');

//ATTENDANT DRIVER TO REPORT INSIDENTS
Route::middleware('auth:api')->post('/report-incident','api\v1\LoginController@reportIncident');
//GET PICKUP POINTS
Route::middleware('auth:api')->get('/get-pickup-points/{id}','api\v1\LoginController@getPickupPoints');
//INSPECTION
Route::middleware('auth:api')->get('/inspection','api\v1\BusMaintenanceController@getInspection');
Route::middleware('auth:api')->post('/save-inspection','api\v1\BusMaintenanceController@storeInspectionComment');
//incidents data
Route::middleware('auth:api')->get('/incident-trip', 'api\v1\BusMaintenanceController@getIncidentTrips');
Route::middleware('auth:api')->get('/incident-students', 'api\v1\BusMaintenanceController@getIncidentStudents');
Route::middleware('auth:api')->get('/incident-parents', 'api\v1\BusMaintenanceController@getIncidentParent');
//GET VEHICLE MAINTENANCE
Route::middleware('auth:api')->get('/routine-maintenance','api\v1\BusMaintenanceController@getRoutineMaintenance');
Route::middleware('auth:api')->post('/routine-maintenance-save','api\v1\BusMaintenanceController@saveRoutineMaintenance');
//inspection
Route::middleware('auth:api')->get('/inspection/get','api\v1\LoginController@getInspection');


//parents
Route::middleware('auth:api')->get('/parentid', 'api\v1\ParentsController@parentId');
Route::middleware('auth:api')->get('/vehiclecoord/{id}', 'api\v1\ParentsController@vehiclechildCoord');
Route::middleware('auth:api')->get('/children/{id}','api\v1\ParentsController@myChildren');
//ONE CHILD
Route::middleware('auth:api')->get('/child/{id}','api\v1\ParentsController@myChild');


//flag off
Route::middleware('auth:api')->post('/flagoff/{id}','api\v1\ParentsController@flagOff');
// attendance data
Route::middleware('auth:api')->get('/attendance-data/{id}', 'api\v1\ParentsController@getAttendanceData');
// school trips
Route::middleware('auth:api')->get('/school-trips/{id}', 'api\v1\ParentsController@schoolTrips');
// get teacher based on school trip
Route::middleware('auth:api')->get('/teacher/{id}', 'api\v1\ParentsController@teacher');
// term
Route::middleware('auth:api')->get('/term', 'api\v1\ParentsController@schoolTerm');
// holidays
Route::middleware('auth:api')->get('/holidays', 'api\v1\ParentsController@holidays');
// termEvents
Route::middleware('auth:api')->get('/events', 'api\v1\ParentsController@termEvents');
// driver info
Route::middleware('auth:api')->get('/driver-info/{id}', 'api\v1\ParentsController@driverInfo');
// Paid invoice
Route::middleware('auth:api')->get('/paid-inv/{id}', 'api\v1\ParentsController@paidInv');
// Unpaid invoice
Route::middleware('auth:api')->get('/unpaid-inv/{id}', 'api\v1\ParentsController@unPaidInv');
// Notifications
Route::middleware('auth:api')->get('/notifictions/{id}', 'api\v1\ParentsController@allNotifs');
// Notifications Mark Read
Route::middleware('auth:api')->get('/mark-as-read/{id}', 'api\v1\ParentsController@markAsRead');
//GET OTHER PARENT
Route::middleware('auth:api')->get('/other-parent/{id}','api\v1\ParentsController@otherParent');
//REVIEW/FEEBACK
Route::middleware('auth:api')->post('/review-store/{id}','api\v1\ParentsController@review');

//school add data and update
Route::middleware('auth:api')->post('/parents/store', 'api\v1\SchoolDataController@getParentData');
Route::middleware('auth:api')->post('/parents/update', 'api\v1\SchoolDataController@updateParentData');
//store pickup
Route::middleware('auth:api')->post('/pickup/{id}','api\v1\ParentsController@storePickup');
Route::middleware('auth:api')->post('/dropoff/{id}','api\v1\ParentsController@storeDropOff');

Route::middleware('auth:api')->post('/students/store', 'api\v1\SchoolDataController@getStudentData');
Route::middleware('auth:api')->post('/students/update', 'api\v1\SchoolDataController@updateStudentData');

//Fees 
Route::middleware('auth:api')->get('/paid-fees/{id}', 'api\v1\SchoolFeesController@paidFess');
Route::middleware('auth:api')->get('/unpaid-fees/{id}', 'api\v1\SchoolFeesController@getUnpaidFess');

//add users
Route::middleware('auth:api')->post('/add-user/{id}', 'api\v1\ParentsController@addUsers');

//Update student profile
Route::middleware('auth:api')->post('/profile-picture/{id}', 'api\v1\ParentsController@updateStudentPhoto');


//Test Notification
Route::middleware('auth:api')->post('/test-notiff','api\v1\ParentsController@testNotif');

// SAVE ARRIVAL 
Route::middleware('auth:api')->post('/save-arrival','api\v1\ParentsController@confirmChildArrival');

//checkout schooltrip
Route::middleware('auth:api')->post('/schooltrip-checkout','api\v1\CheckoutController@getToken');
Route::middleware('auth:api')->post('/schooltrip-payment','api\v1\CheckoutController@updatePayemnt');

Route::middleware('auth:api')->post('/schoolfees-checkout','api\v1\SchoolFeesController@getToken');
Route::middleware('auth:api')->post('/schoolfees-payments','api\v1\SchoolFeesController@updatePayemnt');

Route::middleware('auth:api')->post('/school-fees-refences', 'api\v1\SchoolFeesController@storeMpesaPaymentRefereces');

//incidents
Route::middleware('auth:api')->get('/incidents/get','api\v1\LoginController@getIncidents');

