<?php

namespace App\Http\Controllers;

use App\Models\SchoolTermDate;
use App\Models\TermHoliday;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class TermHolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $term = SchoolTermDate::where('status', '=', 1)->first();

        if (!$term) {
            return redirect()->route('term.create')->with('unsuccess', 'Please add school terms');
        }

        $terms = TermHoliday::where('term_id', '=', $term->id)->get();

        $user = Auth::user();
        
        if ($user->user_type == 'parent') {
            $pNofitications = User::find($user->id)->unreadNotifications;

            $numOfNotifications = count($pNofitications);


            return view('term_holiday.index')->with([
                'terms' => $terms,
                'numOfNotifications' => $numOfNotifications
            ]);
        }

        return view('term_holiday.index')->with([
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

        $term = SchoolTermDate::where('status', '=', 1)->first();

        if (!$term) {
            return redirect()->route('term.create')->with('unsuccess', 'Please add school terms');
        }


        return view('term_holiday.create')->with(['year' => $year, 'term' => $term]);
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

        $term = new TermHoliday();
        $term->name = $request->name;
        $term->start = $request->start;
        $term->ends = $request->end;
        $term->year = $request->year;
        $term->term_id = $request->term_id;

        if ($term->save()) {
            return redirect()->route('term_holiday.index')->with([
                'success' => 'Record saved successfully'
            ]);
        }

        return redirect()->back()->with([
            'unsuccess' => 'System error please try again'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TermHoliday  $termHoliday
     * @return \Illuminate\Http\Response
     */
    public function show(TermHoliday $termHoliday)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TermHoliday  $termHoliday
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $term = TermHoliday::find(Crypt::decrypt($id));

        $schoolterm = SchoolTermDate::where('status', '=', 1)->first();


        return view('term_holiday.edit')->with([
            'term'=> $term,
            'schoolterm' => $schoolterm
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TermHoliday  $termHoliday
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $term = TermHoliday::find($id);
        $term->name = $request->name;
        $term->start = $request->start;
        $term->ends = $request->end;
        $term->year = $request->year;
        $term->term_id = $request->term_id;

        
        if ($term->update()) {
            return redirect()->route('term_holiday.index')->with([
                'success' => 'Record updated successfully'
            ]);
        }

        return redirect()->back()->with([
            'unsuccess' => 'System error please try again'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TermHoliday  $termHoliday
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $term = TermHoliday::find($id);
        $term->status = 0;
        
        if ($term->update()) {
            return redirect()->route('term_holiday.index')->with([
                'success' => 'Record deactivated successfully'
            ]);
        }

        return redirect()->back()->with([
            'unsuccess' => 'System error please try again'
      
        ]);
        
    }

    public function activate(Request $request)
    {
        $term = TermHoliday::find($request->holiday_id);
        $term->status = 1;
        
        if ($term->update()) {
            return redirect()->route('term_holiday.index')->with([
                'success' => 'Record activated successfully'
            ]);
        }

        return redirect()->back()->with([
            'unsuccess' => 'System error please try again'
      
        ]);
        
    }
}
