<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function index()
    {
        $groups = DB::table('class_groups')->get();
        $grades = DB::table('student_classes')->get();
        $streams = DB::table('streams')->get();

        
        return view('grades.index', compact('groups','grades','streams'));
    }

    /**
     * 
     */
    public function groupCreatePage()
    {
        return view('grades.creates.group');
    }


    /**
     * 
     */
    public function groupStore(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        DB::table('class_groups')->insert([
            "name" => $request->name
        ]);

        return redirect()->route('grades_page')->with('success','Record stored succesfully');
    }

    /**
     * 
     */
    public function editGroupPage($id)
    {
        $group = DB::table('class_groups')->find(Crypt::decrypt($id));

        return view('grades.edit.group', compact('group'));
    }

    /**
     * 
     */
    public function groupUpdateStore(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        DB::table('class_groups')->where('id',$request->id)->update([
            "name" => $request->name
        ]);

        return redirect()->route('grades_page')->with('success','Record updated succesfully');
    }


    /**
     * 
     */
    public function gradeCreatePage()
    {
        $groups = DB::table('class_groups')->get();
        return view('grades.creates.grade', compact('groups'));
    }

    public function gradeStore(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'group' => 'required'
        ]);

        if ($request->group == "select...") {
            return redirect()->back()->with('unsuccess','Please select group');
        }

        $grades = explode(',',$request->name);

        foreach ($grades as $grade) {
            DB::table('student_classes')->insert([
                "name" => $grade,
                'group' => $request->group
            ]);
        }

        return redirect()->route('grades_page')->with('success','Record stored succesfully');
    }

    /**
     * 
     */
    public function editGradePage($id)
    {
        $grade = DB::table('student_classes')->find(Crypt::decrypt($id));

        $groups = DB::table('class_groups')->get();

        return view('grades.edit.grade', compact('groups','grade'));
    }

    /**
     * 
     */
    public function gradeUpdateStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'group' => 'required'
        ]);

        DB::table('student_classes')->where('id',$request->id)->update([
            "name" => $request->name,
            'group' => $request->group
        ]);

        return redirect()->route('grades_page')->with('success','Record updated succesfully');
    }


    /**
     * 
     */
    public function streamCreatePage()
    {
        $grades = DB::table('student_classes')->get();
        return view('grades.creates.stream', compact('grades'));
    }

    public function streamStore(Request $request)
    {


        $request->validate([
            'grade' => 'required',
            'streams' => 'required',
            'teacher' => 'required'
        ]);

        if ($request->grade == "select...") {
            return redirect()->back()->with('unsuccess','Please select grade');
        }


        for ($i=0; $i < count($request->teacher); $i++) { 
            DB::table('streams')->insert([
                "name" => $request->streams[$i],
                'class_teacher' => $request->teacher[$i],
                'student_classes_id' => $request->grade
            ]);
        }

        return redirect()->route('grades_page')->with('success','Record stored succesfully');
    }

    /**
     * edit stream page
     */
    public function streamEditPage($id)
    {
        $teachers = User::where('user_type','=','teacher')->get();
        $grades = DB::table('student_classes')->get();
        $stream = DB::table('streams')->find(Crypt::decrypt($id));
        return view('grades.edit.stream', compact('grades','teachers','stream'));
    }

    public function streamUpdateStore(Request $request)
    {

        $request->validate([
            'grade' => 'required',
            'streams' => 'required',
            'teacher' => 'required'
        ]);

        
        DB::table('streams')->where('id',$request->id)->update([
            "name" => $request->streams,
            'class_teacher' => $request->teacher,
            'student_classes_id' => $request->grade
        ]);

        return redirect()->route('grades_page')->with('success','Record stored succesfully');
    }

    /**
     * get teachers for stream selection
     */
    public function getTeachers()
    {
        $teachers = User::where('user_type','=','teacher')->get();
        $final_array = [];
        foreach ($teachers as $teacher) {
            $check = DB::table('streams')->where('class_teacher','=',$teacher->id)->first();

            if (! $check) {
                array_push($final_array, $teacher);
            }
        }
        return response($final_array);
    }

    public function deleteGroup($id)
    {
        $grade = DB::table('student_classes')->where('group','=', Crypt::decrypt($id))->get();
        if ($grade->isNotEmpty()) {
            return redirect()->back()->with('unsuccess','Group has assigned grades.');
        }
        DB::table('class_groups')->where('id','=',Crypt::decrypt($id))->update([
            'status' => 0
        ]);
        return redirect()->back()->with('success','Record deactivated successfully');
    }

    public function activateGroup(Request $request)
    {
        DB::table('class_groups')->where('id','=',$request->group_id)->update([
            'status' => 1
        ]);
        return redirect()->back()->with('success','Record activated successfully');
    }

    public function deleteGrade($id)
    {
        $students = Student::where('grade_id','=', Crypt::decrypt($id))->get();

        if ($students->isNotEmpty()) {
            return redirect()->back()->with('unsuccess','Grade has assigned students.');
        }

        DB::table('student_classes')->where('id','=',Crypt::decrypt($id))->update([
            'status' => 0
        ]);
        return redirect()->back()->with('success','Record deactivated successfully');
    }

    public function activateGrade(Request $request)
    {
        DB::table('student_classes')->where('id','=',$request->grade_id)->update([
            'status' => 1
        ]);
        return redirect()->back()->with('success','Record activated successfully');
    }

    public function deleteStream($id)
    {
        $students = Student::where('stream','=',Crypt::decrypt($id))->get();

        if ($students->isNotEmpty()) {
            return redirect()->back()->with('unsuccess','Stream has assigned students.');
        }

        $stream = Stream::find(Crypt::decrypt($id));

        if ($stream) {
            $stream->status = 0;
            if($stream->update()) {
                return redirect()->back()->with('success','Record deactivated successfully');
            }
        }

        return redirect()->back()->with('unsuccess','System error please try again.');
    }

    public function activateSteam(Request $request)
    {
        $stream = Stream::find($request->stream_id);
        $stream->status = 1;
        if ($stream->update()) {
            return redirect()->back()->with('success','Record activated successfully');
        }
        return redirect()->back()->with('unsuccess','System error please try again.');
    }
}
