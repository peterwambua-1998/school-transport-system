<?php

namespace App\Http\Controllers;

use App\Models\EmailSettings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class EmailSettingsController extends Controller
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

        return view('settings.create')->with([
            'notifications' => $notifications,
           
        ]);
    }

    protected function changeEnv($data = array()){
        if(count($data) > 0){

            // Read .env-file
            $env = file_get_contents(base_path() . '/.env');

            // Split string on every " " and write into array
            $env = preg_split('/\s+/', $env);;

            // Loop through given data
            foreach((array)$data as $key => $value){

                // Loop through .env-data
                foreach($env as $env_key => $env_value){

                    // Turn the value into an array and stop after the first split
                    // So it's not possible to split e.g. the App-Key by accident
                    $entry = explode("=", $env_value, 2);

                    // Check, if new key fits the actual .env-key
                    if($entry[0] == $key){
                        // If yes, overwrite it with the new one
                        $env[$env_key] = $key . "=" . $value;
                    } else {
                        // If not, keep the old one
                        $env[$env_key] = $env_value;
                    }
                }
            }

            // Turn the array back to an String
            $env = implode("\n", $env);

            // And overwrite the .env with the new data
            file_put_contents(base_path() . '/.env', $env);
            
            return true;
        } else {
            return false;
        }
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
            'name' => 'required'
        ]);

        $isSettings = EmailSettings::find(1);
        if (! $isSettings) {
            $settings = new EmailSettings();
            $settings->name = $request->name;
            $settings->email = $request->email;
            $settings->username = $request->username;
            $settings->password = $request->password;
            $settings->port = $request->port;
            $settings->security = $request->security;
            $settings->host = $request->host;
            $settings->SMTPAutoTLS = $request->SMTPAutoTLS;
            $settings->SMTPAuth = $request->SMTPAuth;

            $this->changeEnv([
                'MAIL_MAILER'   => 'smtp',
                'MAIL_HOST'   => $request->host,
                'MAIL_PORT'  => $request->port,
                'MAIL_USERNAME' => $request->username,
                'MAIL_PASSWORD' => $request->password,
                'MAIL_ENCRYPTION' => $request->security,
                'MAIL_FROM_ADDRESS' => $request->email,
                'MAIL_FROM_NAME' => str_replace(' ','',$request->name),
            ]);

           
            if ($settings->save()) {
                return redirect()->back()->with('success', 'Success, your email settings have been saved.');
            
            }
        } else {
            $isSettings->name = $request->name;
            $isSettings->email = $request->email;
            $isSettings->username = $request->username;
            $isSettings->password = $request->password;
            $isSettings->port = $request->port;
            $isSettings->security = $request->security;
            $isSettings->host = $request->host;
            $isSettings->SMTPAutoTLS = $request->SMTPAutoTLS;
            $isSettings->SMTPAuth = $request->SMTPAuth;

            $this->changeEnv([
                'MAIL_MAILER'   => 'smtp',
                'MAIL_HOST'   => $request->host,
                'MAIL_PORT'  => $request->port,
                'MAIL_USERNAME' => $request->username,
                'MAIL_PASSWORD' => $request->password,
                'MAIL_ENCRYPTION' => $request->security,
                'MAIL_FROM_ADDRESS' => $request->email,
                'MAIL_FROM_NAME' => str_replace(' ','',$request->name),
            ]);

            
        
            if ($isSettings->update()) {
                return redirect()->back()->with('success', 'Success, your email settings have been saved.');
            
            }
        }
    }

    

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EmailSettings  $emailSettings
     * @return \Illuminate\Http\Response
     */
    public function show(EmailSettings $emailSettings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EmailSettings  $emailSettings
     * @return \Illuminate\Http\Response
     */
    public function edit(EmailSettings $emailSettings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EmailSettings  $emailSettings
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmailSettings $emailSettings)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EmailSettings  $emailSettings
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmailSettings $emailSettings)
    {
        //
    }
}
