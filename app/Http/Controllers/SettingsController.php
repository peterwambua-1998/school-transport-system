<?php

namespace App\Http\Controllers;

use AfricasTalking\SDK\AfricasTalking;
use App\Models\NotificationSetting;
use App\Models\Settings;
use App\Models\SmsSetting;
use App\Models\Terminology;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\returnSelf;

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

        $notificationSetting = NotificationSetting::find(1);


        if (! $settings) {
            return ;
        }

        return view('settings.create')->with([
            'notifications' => $notifications,
           'settings' => $settings,
           'notificationSetting' => $notificationSetting
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
        if (!$request->lat && !$request->lng) {
            return redirect()->back()->with('unsuccess','Click on map to pin point exact school location.');
        }
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
            $settings->lat = $request->lat;
            $settings->lng = $request->lng;

            
            
            
            if ($request->file('image')) {
                // Delete old image
                if ($settings->company_logo) {
                    Storage::disk('public_uploads')->delete($settings->company_logo);
                }
                // Store image
                $image_path = $request->file('image')->store('logo', 'public_uploads');
                // Save to Database
                $settings->company_logo = $image_path;
            }


            if ($settings->save()) {
                return redirect()->route('notification-settings.create')->with('success', 'Success, your settings have been saved.');
            
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

                $this->middleware('settings');
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
     * 
     */
    public function getAppLinks()
    {
        $links = DB::table('app_links')->find(1);
        return view('settings.applinks', compact('links'));
    }

    /**
     * 
     */
    public function storeAppLinks(Request $request)
    {
        $appLinks = DB::table('app_links')->find(1);

        if ($appLinks) {
            DB::table('app_links')->where('id','=', 1)->update([
                "ios" => $request->ios,
                'android' => $request->android
            ]);

            return redirect()->back()->with('success', 'Record updated succcessfuly.');
        } else {
            DB::table('app_links')->insert([
                "ios" => $request->ios,
                'android' => $request->android
            ]);

            return redirect()->back()->with('success', 'Record updated succcessfuly.');
        }
        return redirect()->back()->with('unsuccess', 'System error try again later');
    }


    public function paymentPage()
    {
        $mpesa = DB::table('mpesa_settings')->find(1);
        $paypal = DB::table('paypal_settings')->find(1);
        
        return view('payments.create', compact('mpesa','paypal'));
    }

    public function paymentStore(Request $request)
    {
        $flag = $request->flag;

        if ($flag == 'mpesa') {
            $mpesa = DB::table('mpesa_settings')->where('id',1);


            if ($mpesa->first()) {
                $mpesa->update([
                    'id' => 1,
                    'key' => $request->key,
                    'secret' => $request->secret,
                    'shortcode' => $request->short_code
                ]);

               
            } else {
                DB::table('mpesa_settings')->insert([
                    'id' => 1,
                    'key' => $request->key,
                    'secret' => $request->secret,
                    'shortcode' => $request->short_code
                ]);
            }
        }

        return redirect()->back()->with('success','Record added successfully');
    }


    public function createFirstSettings()
    {
        return view('new-system-settings.create');
    }

    public function smsSettings() 
    {
        $sms = SmsSetting::find(1);
        return view('sms_settings.create', compact('sms'));
    }

    public function smsSettingsSave(Request $request) 
    {
        $request->validate([
            'user_name' => 'required',
            'api_key' => 'required'
        ]);
        $sms = SmsSetting::find(1);
        if (! $sms) {
            $new_sms = new SmsSetting();
            $new_sms->user_name = $request->user_name;
            $new_sms->api_key = $request->api_key;
            $new_sms->short_code = $request->short_code;
            $new_sms->save();
        } else {
            $sms->user_name = $request->user_name;
            $sms->api_key = $request->api_key;
            $sms->short_code = $request->short_code;
            $sms->update();
        }

        return redirect()->route('sms_settings')->with('success','Settings saved successfully.');
    }

    public function testSms()
    {
        $sms_instance = SmsSetting::find(1);
        $username   = "sandbox";
        $apiKey     = "e929f558072ea4becfc56297c7e9f357fa0282775e4f3b1b738060645a6c9e64";
        $AT = new AfricasTalking($sms_instance->user_name, $sms_instance->api_key);
        $sms = $AT->sms();
        $recipients = '+254715100539';
        $message = "I'm a lumberjack and its ok, I sleep all night and I work all day";
        if ($sms_instance->user_name == 'sandbox') {
            $from = '';
        } else {
            $from = $sms_instance->short_code;
        }
        try {
            

            dd($sms->send([
                'to'      => $recipients,
                'message' => $message,
                'from'    => $from
            ]));
        } catch (\Exception $th) {
            Log::error($th->getMessage());
        }

        dd('peter');
    }
}
