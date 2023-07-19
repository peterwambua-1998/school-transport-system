<?php

namespace App\Http\Controllers;


use App\Models\DefaultMessageSetting;
use Illuminate\Http\Request;

class DefaultMessageSettingController extends Controller
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
            'msg' => 'required'
        ]);

        $isMsgSettings = DefaultMessageSetting::find(1);

        if (! $isMsgSettings) {
            $msgSettings = new DefaultMessageSetting();
            $msgSettings->message = $request->msg;

            if ($msgSettings->save()) {
                return redirect()->back()->with('success', 'settings stored successfully');
            }
            
        } else {
            $msgSettings = DefaultMessageSetting::find(1);
            $msgSettings->message = $request->msg;
            
            if ($msgSettings->update()) {
                return redirect()->back()->with('success', 'Message Settings stored successfully');
            }
        }


        return redirect()->back()->with('unsuccess', 'Sytem error please try again later');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DefaultMessageSetting  $defaultMessageSetting
     * @return \Illuminate\Http\Response
     */
    public function show(DefaultMessageSetting $defaultMessageSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DefaultMessageSetting  $defaultMessageSetting
     * @return \Illuminate\Http\Response
     */
    public function edit(DefaultMessageSetting $defaultMessageSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DefaultMessageSetting  $defaultMessageSetting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DefaultMessageSetting $defaultMessageSetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DefaultMessageSetting  $defaultMessageSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(DefaultMessageSetting $defaultMessageSetting)
    {
        //
    }
}
