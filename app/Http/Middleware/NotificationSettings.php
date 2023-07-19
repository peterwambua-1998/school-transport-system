<?php

namespace App\Http\Middleware;

use App\Models\NotificationSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationSettings
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
        $notificationSetting = NotificationSetting::find(1);

        if (! $notificationSetting) {
            return redirect()->route('notification-settings.create')->with('unsuccess', 'Register notification settings');
        }
        return $next($request);
    }
}
