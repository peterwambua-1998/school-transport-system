<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class WhatsappSettings
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
        $whatsApp =  DB::table('whatsapp_settings')->find(1);

        if (! $whatsApp) {
            return redirect()->route('settings_create')->with('unsuccess', 'Register whatsapp settings');
        }

        return $next($request);
    }
}
