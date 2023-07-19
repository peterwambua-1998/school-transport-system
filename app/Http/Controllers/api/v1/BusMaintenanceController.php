<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\BusMaintenance;
use App\Models\BusMaintenanceImage;
use App\Models\Garage;
use App\Models\Inspection;
use App\Models\InspectionClaim;
use App\Models\StandinBus;
use App\Models\StandinDriver;
use App\Models\Student;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Warranty;
use App\Models\WarrantyClaim;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use stdClass;

class BusMaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
    public function store(Request $request, $id)
    {
        $json = json_decode($request->getContent(), true);

        $driver = auth('api')->user();

        $vehicle = Vehicle::where('id','=',$id)->first();

        //driver stand-in
        if ($driver->user_type == "driver") {
            $check_stand_in_driver = StandinDriver::where('stand_in_driver','=', $driver->id)->where('status','=', 1)->first();
            if ($check_stand_in_driver) {
                $vehicle = Vehicle::where('id', '=', $check_stand_in_driver->stand_in_vehicle)->first();
            }
        }

        $garage = Garage::where('active','=', 1)->first();

        if(! $garage) {
            return abort(404, 'garage not found');
        }

        if (!$vehicle) {
            return abort(404, 'vehicle not found');
        }

        DB::transaction(function () use ($request, $vehicle, $garage, $json) {
            if ($json['status'] == "daily") {
                $maintenance = new BusMaintenance();
                $maintenance->vehicle_id = $vehicle->id;
                $maintenance->shift = $json['shift'];
                $maintenance->description = $json['description'];
                $maintenance->status = $json['status'];
                $maintenance->place_name = $json['place_name'];
                $maintenance->lat = $json['lat'];
                $maintenance->lng = $json['lng'];
                $maintenance->current_km = $json['current_km'];
                $maintenance->garage = $garage->id;
                if ($json["video"]) {
                    $file = $json['video'];
                    $exploded = explode(',', $file, 2); // limit to 2 parts, i.e: find the first comma
                    $encoded = $exploded[1];
                    $file = base64_decode($encoded);
                    //impliment to get mime type/ extension
                    $imageName = 'video/'.Str::random(60).'.'.'mp4';  
                    Storage::disk('public_uploads')->put($imageName, $file);
                    $maintenance->video = $imageName;
                }
                $maintenance->save();

                $vehicle->mileage = $json["current_km"];
                $vehicle->update();
                
                $details = count($json['files']);
        
                for ($i=0; $i < $details; $i++) { 
                    $maintenanceImages = new BusMaintenanceImage();
                    $maintenanceImages->bus_maintenance_id = $maintenance->id;
                    $file = $json['files'][$i];
                    $exploded = explode(',', $file, 2); // limit to 2 parts, i.e: find the first comma
                    $encoded = $exploded[1];
                    $file = base64_decode($encoded);
                    //impliment to get mime type/ extension
                    $imageName = 'maintenance/'.Str::random(60).'.'.'png';  
                    Storage::disk('public_uploads')->put($imageName, $file);
                    $maintenanceImages->path = $imageName;
                    $maintenanceImages->save();
                }
            }

            if ($json['status'] == "off routine") {
                $maintenance = new BusMaintenance();
                $maintenance->vehicle_id = $vehicle->id;
                $maintenance->shift = $json['shift'];
                $maintenance->description = $json['description'];
                $maintenance->status = $json['status'];
                $maintenance->place_name = $json['place_name'];
                $maintenance->current_km = $json['current_km'];
                $maintenance->garage = $garage->id;
                $maintenance->lat = $json['lat'];
                $maintenance->lng = $json['lng'];
                if ($json["video"]) {
                    $file = $json['video'];
                    $exploded = explode(',', $file, 2); // limit to 2 parts, i.e: find the first comma
                    $encoded = $exploded[1];
                    $file = base64_decode($encoded);
                    //impliment to get mime type/ extension
                    $imageName = 'video/'.Str::random(60).'.'.'mp4';  
                    Storage::disk('public_uploads')->put($imageName, $file);
                    $maintenance->video = $imageName;
                }
                $maintenance->save();
               
                $details = count($json['files']);
                if ($json["files"]) {
                    for ($i=0; $i < $details; $i++) { 
                        $maintenanceImages = new BusMaintenanceImage();
                        $maintenanceImages->bus_maintenance_id = $maintenance->id;
                        $file = $json['files'][$i];
                        $exploded = explode(',', $file, 2); // limit to 2 parts, i.e: find the first comma
                        $encoded = $exploded[1];
                        $file = base64_decode($encoded);
                        //impliment to get mime type/ extension
                        $imageName = 'maintenance/'.Str::random(60).'.'.'png';  
                        Storage::disk('public_uploads')->put($imageName, $file);
                        $maintenanceImages->path = $imageName;
                        $maintenanceImages->save();
                    }
                }
            }
        });

        return response('success');
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
     * @param 
     * @return response
     * 
     */
    public function activeGarage()
    {
        $garage = Garage::where('active','=', 1)->first();
        
        return response($garage);
    }
    /**
     * 
     * @param vehicle $id
     * @return history of vehicle maintenance
     */
    public function maintenanceHistory($id)
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

        if (! $vehicle) {
            return response([]);
        }

        $garage = Garage::where('active','=', 1)->first();

        //daily
        $daily = BusMaintenance::where('vehicle_id','=',$vehicle->id)->where('status','=','daily')->orderBy('id','desc')->take(10)->get();

        $daily_array = [];

        foreach ($daily as $key => $maintenance) {
            $inner_array = ["maintenance" => [],"images" => []];

            array_push($inner_array["maintenance"], ["shift" => $maintenance->shift, "description" => $maintenance->description, 'date' => $maintenance->created_at->toDateString(), 'place_name' => $garage->location, 'current_km' => $maintenance->current_km]);

            $inner_array["maintenance"] = ["shift" => $maintenance->shift, "description" => $maintenance->description, 'date' => $maintenance->created_at->toDateString(),'place_name' => $garage->location, 'current_km' => $maintenance->current_km];
            if ($maintenance->video) {
                $inner_array['video'] = asset('store/'.$maintenance->video);
            } else {
                $inner_array['video'] = "";
            }

            foreach ($maintenance->busmaintenanceimages as $key => $value) {
                $image = asset('store/'.$value->path);
                array_push($inner_array["images"], $image);
            }

            array_push($daily_array, $inner_array);
            
        }

        //routine
        $routine = BusMaintenance::where('vehicle_id','=',$vehicle->id)->where('status','=','routine')->orderBy('id','desc')->take(10)->get();

        $routine_array = [];

        foreach ($routine as $key => $maintenance) {
            $inner_array = ["maintenance" => [],"images" => []];

            array_push($inner_array["maintenance"], ["routine_id" => $maintenance->id, "shift" => $maintenance->shift, "description" => $maintenance->description,'date' => $maintenance->created_at->toDateString(), 'place_name' => $garage->location,]);

            $inner_array["maintenance"] = ["routine_id" => $maintenance->id, "shift" => $maintenance->shift, "description" => $maintenance->description,'date' => $maintenance->created_at->toDateString(), 'place_name' => $garage->location];
            if ($maintenance->video) {
                $inner_array['video'] = asset('store/'.$maintenance->video);
            } else {
                $inner_array['video'] = "";
            }
            foreach ($maintenance->busmaintenanceimages as $key => $value) {
                $image = asset('store/'.$value->path);
                array_push($inner_array["images"], $image);
            }
            array_push($routine_array, $inner_array);
        }

        //off routine
        $offroutine = BusMaintenance::where('vehicle_id','=',$vehicle->id)->where('status','=','off routine')->orderBy('id','desc')->take(10)->get();

        $offroutine_array = [];

        foreach ($offroutine as $key => $maintenance) {
            $inner_array = ["maintenance" => [],"images" => []];

            array_push($inner_array["maintenance"], ["shift" => $maintenance->shift, "description" => $maintenance->description, 'date' => $maintenance->created_at->toDateString(), 'place_name'=> $maintenance->place_name]);

            $inner_array["maintenance"] = ["shift" => $maintenance->shift, "description" => $maintenance->description, 'date' => $maintenance->created_at->toDateString(), 'place_name'=> $maintenance->place_name];
            if ($maintenance->video) {
                $inner_array['video'] = asset('store/'.$maintenance->video);
            } else {
                $inner_array['video'] = "";
            }

            foreach ($maintenance->busmaintenanceimages as $key => $value) {
                $image = asset('store/'.$value->path);
                array_push($inner_array["images"], $image);
            }


            array_push($offroutine_array, $inner_array);
        }

        //warranty
        $warranty = Warranty::where('vehicle_id','=', $vehicle->id)->where('status','=','active')->first();
        //last service done
        $prevServices = BusMaintenance::where('vehicle_id','=', $vehicle->id)->where('status','=', 'routine')->orderBy('created_at','desc')->first();
        $current = $vehicle->mileage;
        $next_service = $prevServices->next_service;
        
        return response(["daily" => $daily_array, "routine" => $routine_array, "off routine" => $offroutine_array, 'current_km' => $current, 'warranty' => $warranty ?? null,'garage' => $garage,'service_at' => $next_service]);
    }


    /**
     * show inspection
     */
    public function getInspection()
    {
        $user = auth('api')->user();

        $vehicle = Vehicle::where('driver_id','=',$user->id)->first() ?? Vehicle::where('attendant_id','=',$user->id)->first();

        //driver stand-in
        if ($user->user_type == "driver") {
            $check_stand_in_driver = StandinDriver::where('stand_in_driver','=', $user->id)->where('status','=', 1)->first();
            if ($check_stand_in_driver) {
                $vehicle = Vehicle::where('id', '=', $check_stand_in_driver->stand_in_vehicle)->first();
            }
        }


        if (! $vehicle) {
            return abort(404, 'vehicle not found');
        }
        $inspection = Inspection::where('vehicle_id','=', $vehicle->id)->first();

        if (! $inspection) {
            $obj = new stdClass;
            return response()->json($obj);
        }

        return response($inspection);
    }

    /**
     * store inspection comment
     */
    public function storeInspectionComment(Request $request)
    {
        $user = auth('api')->user();
        $vehicle = Vehicle::where('driver_id', '=', $user->id)->first() ?? Vehicle::where('attendant_id', '=', $user->id)->first();
        //driver stand-in
        if ($user->user_type == "driver") {
            $check_stand_in_driver = StandinDriver::where('stand_in_driver','=', $user->id)->where('status','=', 1)->first();
            if ($check_stand_in_driver) {
                $vehicle = Vehicle::where('id', '=', $check_stand_in_driver->stand_in_vehicle)->first();
            }
        }
        if (! $vehicle) {
            return abort(404, 'vehicle not found');
        }
        $inspection = Inspection::where('vehicle_id','=', $vehicle->id)->orderBy('created_at','desc')->first();

        if (! $inspection) {
            return  abort(404, 'inspection not found');
        }

        $date = date('Y-m-d');
        $check = InspectionClaim::where('inspection_id','=',$inspection->id)->where('created_at','LIKE', '%'. $date.'%')->first();
        if ($check) {
            $check->delete();
        }
        $inpsectionClaim = new InspectionClaim();
        $inpsectionClaim->inspection_id = $inspection->id;
        $inpsectionClaim->comment = $request->comment;
        $inspection->comment = $request->comment;
        $inspection->update();
        if ($inpsectionClaim->save()) {
            return response('success');
        }

        return response('system error please try again');
    }

    public function getIncidentTrips()
    {
        $user = auth('api')->user();
        $vehicle = Vehicle::where('driver_id','=',$user->id)->first() ?? Vehicle::where('attendant_id','=',$user->id)->first();
        //driver stand-in
        if ($user->user_type == "driver") {
            $check_stand_in_driver = StandinDriver::where('stand_in_driver','=', $user->id)->where('status','=', 1)->first();
            if ($check_stand_in_driver) {
                $vehicle = Vehicle::where('id', '=', $check_stand_in_driver->stand_in_vehicle)->first();
            }
        }
        
        $trips = Trip::where('vehicle_id','=',$vehicle->id)->get();
        $final_array = [];
        foreach ($trips as $key => $trip) {
            $obj = new stdClass;
            $obj->trip_id = $trip->id;
            $obj->trip_name = $trip->title;
            $obj->time = $trip->time;
            array_push($final_array, $obj);
        }
        return response($final_array);
    }


    public function getIncidentStudents()
    {
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

        $students_vehicle = DB::table('vehicle_students')->where('vehicle_id','=',$vehicle->id)->get();
        $final_array = [];
        foreach ($students_vehicle as $key => $st_vh) {
            $student = Student::find($st_vh->student_id);
            if (count($final_array) > 1) {
                $ch = $final_array[$student->id] ?? null;
                if ($ch) {
                    continue;
                }
            }
            
            $obj = new stdClass;
            $obj->student_id = $student->id;
            $obj->name = $student->first_name .' ' . $student->last_name;
            $final_array[$student->id] = $obj;
        }
        return response(array_values($final_array));
    }
    
    public function getIncidentParent()
    {
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
        $students_vehicle = DB::table('vehicle_students')->where('vehicle_id','=',$vehicle->id)->get();
        $final_array = [];
        foreach ($students_vehicle as $key => $st_vh) {
            $student = Student::find($st_vh->student_id);
            $parent = User::find('id','=',$student->parent_id);
            if (count($final_array) > 1) {
                $ch = $final_array[$student->id] ?? null;
                if ($ch) {
                    continue;
                }
            }
            
            $obj = new stdClass;
            $obj->student_id = $student->id;
            $obj->parent_id = $parent->id;
            $obj->parent_name = $parent->name;
            $final_array[$student->id] = $obj;
        }
        return response(array_values($final_array));
    }
    /**
     * get routine maintenance
    */
    public function getRoutineMaintenance() {
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



        if (! $vehicle) {
            return abort(404, 'Not assigned vehicle');
        }

        $routine_m = BusMaintenance::where('vehicle_id','=', $vehicle->id)->where('status','=','routine')->get(['id','place_name','garage'])->last();

        if ($routine_m) {
            $obj = new stdClass;
            $obj->routine_id = $routine_m->id;
            $obj->garage_id = $routine_m->garage;
            $obj->place_name = $routine_m->place_name;

            return response()->json($obj);
        }
        return response()->json();
        
    }


    public function saveRoutineMaintenance(Request $request)  
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
        
        if (! $vehicle) {
            return abort(404, 'Not assigned vehicle');
        }
        $garage = Garage::where('active','=', 1)->first();

        if(! $garage) {
            return abort(404, 'garage not found');
        }
        DB::transaction(function () use ($request, $vehicle, $garage, $json) {
            $maintenance = BusMaintenance::find($json['routine_id']);
            $maintenance->shift = date('a');
            $maintenance->description = $json['comment'];
            $maintenance->current_km = $json['current_km'];
            $maintenance->amount = $json['amount'] ?? 0;
            if ($maintenance->video) {
                Storage::disk('public_uploads')->delete($maintenance->video);
            }
            if ($json["video"]) {
                $file = $json['video'];
                $exploded = explode(',', $file, 2); // limit to 2 parts, i.e: find the first comma
                $encoded = $exploded[1];
                $file = base64_decode($encoded); 
                //impliment to get mime type/ extension
                $imageName = 'video/'.Str::random(60).'.'.'mp4';  
                Storage::disk('public_uploads')->put($imageName, $file);
                $maintenance->video = $imageName;
            }

            $vehicle->mileage = $json["current_km"];
            $vehicle->update();

            $warranty = Warranty::where('vehicle_id','=', $vehicle->id)->where('status','=', 'active')->first();
            if ($warranty) {
                $warrantyClaims = new WarrantyClaim();
                $warrantyClaims->warranty_id = $warranty->id;
                $warrantyClaims->comment = $json["comment"];
                $warrantyClaims->date = date('Y-m-d');
                $warrantyClaims->mileage = $json["current_km"];
                $warrantyClaims->recorded_by = auth('api')->user()->id;
                $warrantyClaims->save();
            }
        

            $maintenance->update();
            
            $details = count($json['files']);
    
            for ($i=0; $i < $details; $i++) { 
                $maintenanceImages = new BusMaintenanceImage();
                $maintenanceImages->bus_maintenance_id = $maintenance->id;
                $file = $json['files'][$i];
                $exploded = explode(',', $file, 2); // limit to 2 parts, i.e: find the first comma
                $encoded = $exploded[1];
                $file = base64_decode($encoded);
                //impliment to get mime type/ extension
                $imageName = 'maintenance/'.Str::random(60).'.'.'jpeg';  
                Storage::disk('public_uploads')->put($imageName, $file);
                $maintenanceImages->path = $imageName;
                $maintenanceImages->save();
            }

            $prev_maintenance = BusMaintenance::where('status','=', 'routine')->orderBy('created_at','desc')->first();
            if ($prev_maintenance) {
                $last_service = $prev_maintenance->current_km;
            } else {
                $last_service = $vehicle->last_service;
            }

            $next_maintainance = new BusMaintenance();
            $next_maintainance->vehicle_id = $vehicle->id;
            $next_maintainance->garage = $garage->id; 
            $next_maintainance->status = 'routine'; 
            $next_maintainance->place_name = $garage->location;
            $next_maintainance->next_service = $json['current_km'] + $vehicle->service_interval;
            $next_maintainance->last_service = $last_service;
            $next_maintainance->save();
        });

        return response('success');
    }

    public function postRoutineMaintenanceImage(Request $request) 
    {
        $request->validate([
            'file' => 'required|file|mimetypes:video/mp4'
        ]);

        $path = $request->file('file')->store('videos','public_uploads'); 
        $maintenance = BusMaintenance::find($request->maintenance);
        $maintenance->video = $path;
        $maintenance->video_duration = $request->duration;
        if ($maintenance->update()) {
            return response(1);
        }
        return response(0);
    }

    public function warranties()
    {
        $driver = auth('api')->user();
        $vehicle = Vehicle::where('driver_id','=', $driver->id)->first();
        //get vehicle warranties ie: parts and vehicle
        $warranties = Warranty::where('vehicle_id','=', $vehicle->id)->orWhere('status','=','vehicle')->where('status','=', 'parts')->get();
        return response($warranties);
    }
}
