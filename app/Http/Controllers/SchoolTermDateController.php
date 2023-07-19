<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateStudentDetails;
use App\Models\SchoolTermDate;
use App\Models\Student;
use App\Models\TermHoliday;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class SchoolTermDateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $terms = SchoolTermDate::orderBy('created_at', 'DESC')->get();

        $parent = Auth::user();
        if ($parent->user_type == 'parent') {
            $pNofitications = User::find($parent->id)->unreadNotifications;

            $numOfNotifications = count($pNofitications);

            return view('terms.index')->with([
                'terms' => $terms,
                'numOfNotifications' => $numOfNotifications
            ]);
        }
        

        return view('terms.index')->with([
            'terms' => $terms
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $year = date('Y');
        return view('terms.create')->with('year', $year);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' =>'required',
            'start' => 'required',
            'end' => 'required'
        ]);
        //active term
        $ac_tm = SchoolTermDate::where('status','=', 1)->first();
        if ($ac_tm) {
            $ac_tm->status = 0;
            $ac_tm->update();
        }


        $term = new SchoolTermDate();
        $term->name = $request->name;
        $term->start = $request->start;
        $term->ends = $request->end;
        $term->year = $request->year;
        $term->status = 1;

        

        UpdateStudentDetails::dispatch();

        if ($term->save()) {
            return redirect()->route('term.index')->with([
                'success' => 'Term saved successfully'
            ]);
        }

        return redirect()->back()->with([
            'unsuccess' => 'System error please try again'
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SchoolTermDate  $schoolTermDate
     * @return \Illuminate\Http\Response
     */
    public function show(SchoolTermDate $schoolTermDate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SchoolTermDate  $schoolTermDate
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $term = SchoolTermDate::find(Crypt::decrypt($id));

        return view('terms.edit')->with([
            'term'=> $term
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SchoolTermDate  $schoolTermDate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $term = SchoolTermDate::find($id);
        $term->name = $request->name;
        $term->start = $request->start;
        $term->ends = $request->end;
        $term->year = $request->year;

        
        if ($term->update()) {
            return redirect()->route('term.index')->with([
                'success' => 'Term updated successfully'
            ]);
        }

        return redirect()->back()->with([
            'unsuccess' => 'System error please try again'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SchoolTermDate  $schoolTermDate
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $term = SchoolTermDate::find($id);

        $holidays = TermHoliday::where('term_id', '=', $term->id)->get();

        foreach ($holidays as $holiday) {
            $holiday->delete();
        }
        
        if ($term->delete()) {
            return redirect()->route('term.index')->with([
                'success' => 'Term deleted successfully'
            ]);
        }

        return redirect()->back()->with([
            'unsuccess' => 'System error please try again'
        ]);
    }

    public function activateTerm(Request $request)
    {


        $term = SchoolTermDate::find($request->term_id);
        $term->status = $request->status;

        if ($term->update()) {
            return response('change saved');
        }

        return response('System error please try again');

    }
}
