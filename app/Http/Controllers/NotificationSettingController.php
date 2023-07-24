<?php

namespace App\Http\Controllers;

use App\Models\NotificationSetting;
use Illuminate\Http\Request;

class NotificationSettingController extends Controller
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
        $notificationSetting = NotificationSetting::find(1);

        return view('settings.notifications')->with('notificationSetting',$notificationSetting);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $notificationSetting = NotificationSetting::find(1);


        if (!$notificationSetting) {
            $notificationSettings = new NotificationSetting();
            $notificationSettings->insurance_send_at = $request->insurance_send_at;
            $notificationSettings->inspection_send_at = $request->inspection_send_at;
            $notificationSettings->dl_send_at = $request->dl_send_at;
            $notificationSettings->insurance_send_at_two = $request->insurance_send_at_two;
            $notificationSettings->inspection_send_at_two = $request->inspection_send_at_two;
            $notificationSettings->dl_send_at_two = $request->dl_send_at_two;
            $notificationSettings->insurance_unit = $request->insurance_unit;
            $notificationSettings->license_unit = $request->license_unit;
            $notificationSettings->inspection_unit = $request->inspection_unit;
            $notificationSettings->value = $request->pickup_value;
            if($notificationSettings->save()){
                return redirect()->back()->with('success', 'Record stored successfully.');
            }
            return redirect()->back()->with('unsuccess', 'System error please try again');
            
        } else {
            $notificationSetting->insurance_send_at = $request->insurance_send_at;
            $notificationSetting->inspection_send_at = $request->inspection_send_at;
            $notificationSetting->dl_send_at = $request->dl_send_at;
            $notificationSetting->insurance_send_at_two = $request->insurance_send_at_two;
            $notificationSetting->inspection_send_at_two = $request->inspection_send_at_two;
            $notificationSetting->dl_send_at_two = $request->dl_send_at_two;
            $notificationSetting->insurance_unit = $request->insurance_unit;
            $notificationSetting->license_unit = $request->license_unit;
            $notificationSetting->inspection_unit = $request->inspection_unit;
            $notificationSetting->value = $request->pickup_value;
            if($notificationSetting->update()){
                return redirect()->back()->with('success', 'Record updated successfully.');
            }
            return redirect()->back()->with('unsuccess', 'System error please try again');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NotificationSetting  $notificationSetting
     * @return \Illuminate\Http\Response
     */
    public function show(NotificationSetting $notificationSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NotificationSetting  $notificationSetting
     * @return \Illuminate\Http\Response
     */
    public function edit(NotificationSetting $notificationSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NotificationSetting  $notificationSetting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NotificationSetting $notificationSetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NotificationSetting  $notificationSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(NotificationSetting $notificationSetting)
    {
        //
    }
}
