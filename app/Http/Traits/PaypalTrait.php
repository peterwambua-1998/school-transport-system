<?php

namespace App\Http\Traits;

trait PaypalTrait {
    public function getKeys()
    {
        return [
            'client_id' => config('services.paypal.paypal_client'),
            'app_secret' => config('services.paypal.app_secret'),
        ];
    }


    public function urls()
    {
        return [
            "sandbox" => "https://api-m.sandbox.paypal.com", 
            "production" => "https://api-m.paypal.com"
        ];
    }
}