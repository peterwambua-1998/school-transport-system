<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Notifications\GeneratedPassword;
use App\Models\SAndT;
use App\Models\Student;
use App\Models\User;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class SchoolDataController extends Controller
{
    public function getParentData(Request $request)
    {
        $num = count($request->id);

        for ($i=0; $i < $num; $i++) { 
            $generator = new ComputerPasswordGenerator();

            $generator->setLowercase()->setNumbers(false)->setSymbols(false)->setLength(6);

            $password = $generator->generatePassword();

            $parent = new User();
            $parent->name = $request->parnt_name[$i];
            $parent->user_type = 'parent';
            $parent->password = Hash::make($password);
            $parent->email = $request->parnt_email[$i];
            $parent->phone_num = $request->parnt_phone[$i];
            $parent->id_num = $request->id_num[$i];
                
            $parent->save();

            Notification::send($parent, new GeneratedPassword($password));
        }

        return response(['msg' => 'parents data was added successfully']);
        
    }


    public function getStudentData(Request $request)
    {

        $num = count($request->id);

        for ($i=0; $i < $num; $i++) { 
            $student = new Student();
            $student->vehicle_id = $request->vehicle_id[$i];
            $student->parent_id = $request->parent_id[$i];
            $student->first_name = $request->fname[$i];
            $student->last_name = $request->lname[$i];
            $student->grade = $request->grade[$i];
            $student->add_num = $request->add_num[$i];
            $student->lat = $request->lat[$i];
            $student->lng = $request->lng[$i];
    
            $trips = $request->trip_id[$i];
                    
    
            $student->save();
    
    
            foreach ($trips as $trip) {
                $stdtrip = new SAndT();
                $stdtrip->student_id = $student->id;
                $stdtrip->trip_id = $trip;
                $stdtrip->save();
            }
        }
        
        return response(['msg' => 'student data was added successfully']);

    }


    public function updateParentData(Request $request)
    {
        $num = count($request->id);

        for ($i=0; $i < $num; $i++) { 
           

            $parent = User::find($request->id[$i]);
            $parent->name = $request->parnt_name[$i];
            $parent->email = $request->parnt_email[$i];
            $parent->phone_num = $request->parnt_phone[$i];
            $parent->id_num = $request->id_num[$i];
                
            $parent->update();

            
        }

        return response(['msg' => 'parents update was successful']);

    }

    public function updateStudentData(Request $request)
    {

        $num = count($request->id);

        for ($i=0; $i < $num; $i++) { 
            $student = Student::find($request->id[$i]);
            $student->vehicle_id = $request->vehicle_id[$i];
            $student->parent_id = $request->parent_id[$i];
            $student->first_name = $request->fname[$i];
            $student->last_name = $request->lname[$i];
            $student->grade = $request->grade[$i];
            $student->add_num = $request->add_num[$i];
            $student->lat = $request->lat[$i];
            $student->lng = $request->lng[$i];
    
            $trips = $request->trip_id[$i];
                    
    
            $student->update();


            $sandt = SAndT::where('student_id', '=', $student->id)->get();

            foreach ($sandt as $s) {
                $s->delete();
            }

            $trips = $request->trip_id;

            foreach ($trips as $trip) {
                $stdtrip = new SAndT();
                $stdtrip->student_id = $student->id;
                $stdtrip->trip_id = $trip;
                $stdtrip->save();
            }
    
        }

        return response(['msg' => 'students update was successful']);
        
    }
}
