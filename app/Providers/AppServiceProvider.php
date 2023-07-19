<?php

namespace App\Providers;

use App\Events\DlExpired;
use App\Events\ExpiredLicense;
use App\Events\InspectionDate;
use App\Events\InsuranceExpired;
use App\Models\ClassGroup;
use App\Models\DefaultMessageSetting;
use App\Models\DriverLicence;
use App\Models\EmailSetting;
use App\Models\Inspection;
use App\Models\Insurance;
use App\Models\NotificationSetting;
use App\Models\PaymentGatewaySetting;
use App\Models\Terminology;
use App\Models\SchoolTermDate;
use App\Models\Settings;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\WhatsappSetting;
use App\Notifications\DlExpredNotification;
use App\Notifications\InspectionDateNotification;
use App\Notifications\InsuraceExpiredNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {   
        //get user notifications
        
        //check if term is expired
    
        $msg_create_term = null;
        $term = SchoolTermDate::where('status','=', 1)->first();
        if ($term) {
            $term_end = Carbon::createFromFormat('Y-m-d', $term->ends);
            $today = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
            if ($today->gte($term_end)) {
                $term->status = 0;
                $term->update();
                $msg_create_term = 'Please add school term';
            }
        } else {
            $msg_create_term = "Please add school term.";
        }
       

        //check that notification settings have been created
        $notificationSetting = NotificationSetting::find(1);
        $users = User::where('user_type', 'LIKE', 'office staff')
        ->orWhere('user_type', 'LIKE', 'admin')
        ->orWhere('user_type', 'LIKE', 'supervisor')
        ->orWhere('user_type', 'LIKE', 'manager')
        ->orWhere('user_type', 'LIKE', 'office_executive')
        ->get();

        
        //check insurance for all vehicles
        $vehicles = Vehicle::all();
        foreach ($vehicles as $key => $vehicle) {
            $insurance = Insurance::where('vehicle_id','=',$vehicle->id)->where('status','=',1)->first();
            if ($insurance) {
                $days_to_send_before_exp = Carbon::createFromFormat('Y-m-d', $insurance->exp_date)->subDays($notificationSetting->insurance_send_at);
                $days_to_send_before_exp_two = Carbon::createFromFormat('Y-m-d', $insurance->exp_date)->subDays($notificationSetting->insurance_send_at_two);
                $today = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));

                if ($days_to_send_before_exp->eq($today) && $insurance->notification_send == 0) {
                    
                    Notification::send($users, new InsuraceExpiredNotification($insurance, $vehicle));
                    $insurance->notification_send = 1;
                    $insurance->update();
                }

                if ($days_to_send_before_exp_two->eq($today) && $insurance->notification_send_two == 0) {
                    Notification::send($users, new InsuraceExpiredNotification($insurance, $vehicle));
                    $insurance->notification_send_two = 1;
                    $insurance->update();
                }
            }

            //vehicle inspection
            $inspection = Inspection::where('vehicle_id','=', $vehicle->id)->orderBy('created_at','desc')->first();
            if ($inspection) {
                $days_to_send_before_exp = Carbon::createFromFormat('Y-m-d', $inspection->next_inspection)->subDays($notificationSetting->inspection_send_at);
                $days_to_send_before_exp_two = Carbon::createFromFormat('Y-m-d', $inspection->next_inspection)->subDays($notificationSetting->inspection_send_at_two);
                $today = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));

                if ($days_to_send_before_exp->eq($today) && $inspection->notification_send == 0) {
                    $driver = User::find($vehicle->driver_id);
                    $attendant = User::find($vehicle->attendant_id);
                    
                    Notification::send($users, new InspectionDateNotification($inspection, $vehicle, $driver));
                    $driver->notify(new InspectionDateNotification($inspection, $vehicle, $driver));
                    $attendant->notify(new InspectionDateNotification($inspection, $vehicle, $driver));
                    $inspection->notification_send = 1;
                    $inspection->update();
                }

                if ($days_to_send_before_exp_two->eq($today) && $inspection->notification_send_two == 0) {
                    $driver = User::find($vehicle->driver_id);
                    $attendant = User::find($vehicle->attendant_id);
                    Notification::send($users, new InspectionDateNotification($inspection, $vehicle, $driver));
                    $driver->notify(new InspectionDateNotification($inspection, $vehicle, $driver));
                    $attendant->notify(new InspectionDateNotification($inspection, $vehicle, $driver));
                    $inspection->notification_send_two = 1;
                    $inspection->update();
                }
            }

        }

        //check Driver license
        $drivers = User::where('user_type','=','driver')->get();
        foreach ($drivers as $key => $driver) {
            $licence = DriverLicence::where('driver_id','=', $driver->id)->first();
            if ($licence) {
                $days_to_send_before_exp = Carbon::createFromFormat('Y-m-d', $licence->exp_date)->subDays($notificationSetting->dl_send_at);
                $days_to_send_before_exp_two = Carbon::createFromFormat('Y-m-d', $licence->exp_date)->subDays($notificationSetting->dl_send_at_two);
                $today = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));

                if ($days_to_send_before_exp->eq($today)  && $licence->notification_send == 0) {
                    $driver = User::find($licence->driver_id);
                    $driver->notify(new DlExpredNotification($licence, $driver));
                    Notification::send($users, new DlExpredNotification($licence, $driver));
                    $licence->notification_send = 1;
                    $licence->update();
                }

                if ($days_to_send_before_exp_two->eq($today)  && $licence->notification_send_two == 0) {
                    $driver = User::find($licence->driver_id);
                    $driver->notify(new DlExpredNotification($licence, $driver));
                    Notification::send($users, new DlExpredNotification($licence, $driver));
                    $licence->notification_send_two = 1;
                    $licence->update();
                }
                
            }
        }

        //check Inspection if is due
        foreach ($vehicles as $key => $vehicle) {
            
        }

        //check warranty
        $settings = Settings::find(1);
        $msgSettings = DefaultMessageSetting::find(1);
        $paySettings = PaymentGatewaySetting::find(1);
        $emailSettings = EmailSetting::find(1);
        $whatsapp = WhatsappSetting::find(1);
        $tr = Terminology::find(1);
        $links = DB::table('app_links')->find(1);
      
        View::share(['notificationSetting' => $notificationSetting,'whatsapp'=>$whatsapp,'tr'=>$tr,'links'=>$links,'settings' => $settings, 'msgSettings' => $msgSettings, 'paySettings' => $paySettings, 'emailSettings' => $emailSettings,'msg_create_term' => $msg_create_term]); 
        
    }
}
