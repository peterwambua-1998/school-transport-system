<?php

namespace App\Http\Middleware;

use App\Models\NotificationSetting;
use App\Models\Settings;
use App\Models\WhatsappSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $settings = Settings::find(1);
        
        if (! $settings) {
            return redirect()->route('first_settings')->with('unsuccess', 'Register system settings');
        }

        
        /*
        

        $notificationSetting = NotificationSetting::find(1);

        if (! $notificationSetting) {
            return redirect()->route('notification-settings.create')->with('unsuccess', 'Register notification settings');
        }
        */
        

        return $next($request);
    }
}
