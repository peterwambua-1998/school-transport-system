<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
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
        $user = Auth::user();

        $notifications = User::find($user->id)->unreadNotifications;

        $settings = Settings::find(1);

        if (!$settings) {
            $settings = new Settings();
            $settings->company_name = 'company name';
            $settings->company_pnum = '00000';
            $settings->company_address = '';
            $settings->company_email = 'company@mail.com';
            $settings->currency = 'currecy';
            $settings->lat = '-1.2832533';
            $settings->lng = '36.8172449';
            $settings->time_zone = '';
            $settings->save();

            return view('settings.create')->with([
                'notifications' => $notifications,
               'settings' => $settings
            ]);


        }

        

        return view('settings.create')->with([
            'notifications' => $notifications,
           'settings' => $settings
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

        //dd($request);
        $request->validate([
            'company_name' => 'required',
        ]);

        

        $isSettings = Settings::find(1);
        if (! $isSettings) {
            $settings = new Settings();
            $settings->company_name = $request->company_name;
            $settings->company_pnum = $request->company_pnum;
            $settings->company_email = $request->company_email;
            $settings->company_address = $request->company_address;
            $settings->currency = $request->currency;
            $settings->time_zone = $request->time_zone;

           
            
            if ($request->file('image')) {
                // Delete old image
                if ($settings->company_logo) {
                    Storage::disk('public_uploads')->delete($settings->company_logo);
                }

                // Store image
                $image_path = $request->file('image')->store('logo', 'public_uploads');

                dd($image_path);

                // Save to Database
                $settings->company_logo = $image_path;
            }


            if ($settings->save()) {
                return redirect()->back()->with('success', 'Success, your settings have been saved.');
            
            }
        } else {
            $isSettings->company_name = $request->company_name;
            $isSettings->company_pnum = $request->company_pnum;
            $isSettings->company_email = $request->company_email;
            $isSettings->company_address = $request->company_address;
            $isSettings->currency = $request->currency;
            $isSettings->time_zone = $request->time_zone;

            
           

            //dd($request->file('image'));

            if ($request->file('image')) {
                // Delete old image
                if ($isSettings->company_logo) {
                    Storage::disk('public_uploads')->delete($isSettings->company_logo);
                }

                // Store image
                $image_path = $request->file('image')->store('logo', 'public_uploads');

                // Save to Database
                $isSettings->company_logo = $image_path;
            }


            if ($isSettings->update()) {
                return redirect()->back()->with('success', 'Success, your settings have been saved.');
            
            }
        }

        

        return redirect()->back()->with('unsuccess', 'System error try again later');
        
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function show(Settings $settings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function edit(Settings $settings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Settings $settings)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function destroy(Settings $settings)
    {
        //
    }

    /**
     * save center to used on all apps
     */
    public function saveCenterMap(Request $request) {
        if ($request->settings_id != 'not settings') {
            $settings = Settings::find($request->settings_id);
            $settings->lat = $request->lat;
            $settings->lng = $request->lng;


            if ($settings->save()){
                return redirect()->back()->with('success', 'Success, your settings have been saved.');
            }
        }

        return redirect()->back()->with('unsuccess', 'System error try again later');

    }

    /**
     * go to view settings.applinks
     */
    public function getAppLinks()
    {
        dd('p');
        return view('settings.applinks');
    }

    /***
     * store app links
     */
    public function storeAppLinks(Request $request)
    {
        $request->validate([
            'ios' => 'required',
            'android' => 'required'
        ]);

        $applinks = DB::table('app_links')->find(1);

        if ($applinks) {
            $applinks->update([
                'ios' => $request->ios,
                'android' => $request->android
            ]);

            return redirect()->back()->with('success','Record updated successfuly');

        } else {
            DB::table('app_links')->insert([
                'ios' => $request->ios,
                'android' => $request->android
            ]);

            return redirect()->back()->with('success','Record added successfuly');
        }

        return redirect()->back()->with('unsuccess','Sytem error please try again');
    }
}
