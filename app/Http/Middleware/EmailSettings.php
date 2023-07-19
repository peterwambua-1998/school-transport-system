<?php

namespace App\Http\Middleware;

use App\Models\EmailSetting;
use Closure;
use Illuminate\Http\Request;

class EmailSettings
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
        $email = EmailSetting::find(1);
        if (! $email) {
            return redirect()->route('settings_create')->with('unsuccess', 'Register email settings');
        }
        return $next($request);
    }
}
