<?php

namespace App\Http\Controllers;

use App\Models\WhatsappSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhatsappSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        $request->validate([
            'sid' => 'required',
            'token' => 'required',
            'p_num' => 'required'
        ]);

        $whatsapp = new WhatsappSetting();
        $whatsapp->sid = $request->sid;
        $whatsapp->token = $request->token;
        $whatsapp->twilio_num = $request->p_num;

        if ($whatsapp->save()) {
            return redirect()->back()->with('success', 'Success, your whatsapp settings have been saved.');
        }

        return redirect()->back()->with('unsuccess', 'System error please try again');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WhatsappSetting  $whatsappSetting
     * @return \Illuminate\Http\Response
     */
    public function show(WhatsappSetting $whatsappSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WhatsappSetting  $whatsappSetting
     * @return \Illuminate\Http\Response
     */
    public function edit(WhatsappSetting $whatsappSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WhatsappSetting  $whatsappSetting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WhatsappSetting $whatsappSetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WhatsappSetting  $whatsappSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(WhatsappSetting $whatsappSetting)
    {
        //
    }

    public function chatifRedirect()
    {
       


        if (Auth::user()->user_type == 'parent') {
            return redirect()->route('phome');
        } else if (Auth::user()->user_type == 'driver'){
            return redirect()->route('driverlogin_home');
              
        } else {
            return redirect('/');
        }
    }
}
