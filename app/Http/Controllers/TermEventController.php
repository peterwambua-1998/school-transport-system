<?php

namespace App\Http\Controllers;

use App\Jobs\SendWhatsappMessage;
use App\Models\SchoolTermDate;
use App\Models\TermEvent;
use App\Models\WhatsappSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class TermEventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $schoolterm = SchoolTermDate::where('status', '=', 1)->first();

      	if (! $schoolterm) {
            return redirect()->route('term.create')->with('unsuccess', 'Please create term and make it active');
        }
        
	    $terms = TermEvent::where('term_id', '=', $schoolterm->id)->get();

        return view('term_events.index')->with([
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
        $schoolterm = SchoolTermDate::where('status', '=', 1)->first();
        $grades = DB::table('student_classes')->get();

        return view('term_events.create')->with([
            'schoolterm' => $schoolterm,
            'grades' => $grades
        ]);
        
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
            'name' => 'required',
        ]);

        $event = new TermEvent();
        $event->name = $request->name;
        $event->start = $request->start;
        $event->ends = $request->ends;
        $event->start_time = $request->start_time;
        $event->end_time = $request->end_time;
        $event->term_id = $request->term_id;
        $event->year = $request->year;
        $event->location = $request->location;
        $event->within = $request->within;

        if ($request->within == "select...") {
            return redirect()->back()->with('unsuccess','Select transport method to be used');
        }  
        if ($request->within == "no" && $request->transport == "select...") {
            return redirect()->back()->with('unsuccess','Select transport method to be used');
        }  
        if($request->within == "yes") {
            $event->transport = 'school';
        }

        if($request->within == "no") {
            $event->transport = $request->transport;
        }


        if ($event->save()) {
            for ($i=0; $i < count($request->grades); $i++) { 
                DB::table('event_grades')->insert([
                    'event_id' => $event->id,
                    'grade' => $request->grades[$i],
                ]);
            }

            return redirect()->route('term_events.index')->with('success', 'Record added successfully');
        }

        return redirect()->back()->with('unsuccess', 'System error please try again');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TermEvent  $termEvent
     * @return \Illuminate\Http\Response
     */
    public function show(TermEvent $termEvent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TermEvent  $termEvent
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $term = TermEvent::find(Crypt::decrypt($id));

        $schoolterm = SchoolTermDate::where('id', '=', $term->term_id)->first();
        $grades = DB::table('student_classes')->get();

        return view('term_events.edit')->with([
            'term' => $term,
            'schoolterm' => $schoolterm,
            'grades' => $grades
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TermEvent  $termEvent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $event = TermEvent::find($id);
        $event->name = $request->name;
        $event->start = $request->start;
        $event->ends = $request->ends;
        $event->start_time = $request->start_time;
        $event->end_time = $request->end_time;
        $event->term_id = $request->term_id;
        $event->year = $request->year;
        $event->location = $request->location;
        if ($request->within == "select...") {
            return redirect()->back()->with('unsuccess','Select transport method to be used');
        }  
        if ($request->within == 'no') {
            $event->transport = $request->transport;
            $event->within = $request->within;
        } else {
            $event->transport = 'school';
            $event->within = $request->within;
        }

        DB::table('event_grades')->where('event_id','=', $event->id)->delete();

        if ($event->update()) {

            for ($i=0; $i < count($request->grades); $i++) { 
                DB::table('event_grades')->insert([
                    'event_id' => $event->id,
                    'grade' => $request->grades[$i],
                ]);
            }
            return redirect()->route('term_events.index')->with('success', 'Record updated successfully');
        }

        return redirect()->back()->with('unsuccess', 'System error please try again');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TermEvent  $termEvent
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event = TermEvent::find($id);

        if (! $event) {
            return redirect()->back()->with('unsuccess', 'Event not found');
            
        }
        
        $event->status = 0;
        if ($event->update()) {
            return redirect()->route('term_events.index')->with('success', 'Record deactivated successfully');
        }

        return redirect()->back()->with('unsuccess', 'System error please try again');

    }

    public function activate(Request $request)
    {
        $event = TermEvent::find($request->event_id);

        if (! $event) {
            return redirect()->back()->with('unsuccess', 'Event not found');
            
        }
        
        $event->status = 1;
        if ($event->update()) {
            return redirect()->route('term_events.index')->with('success', 'Record activated successfully');
        }

        return redirect()->back()->with('unsuccess', 'System error please try again');

    }


    public function sendMesgWhatsapp($id)
    {
        $whatsapp = WhatsappSetting::find(1);

        if (! $whatsapp) {
            return response(0);
        }

        $ev = TermEvent::find($id);

        SendWhatsappMessage::dispatch($ev);

        return response('message sent');
    }
}
