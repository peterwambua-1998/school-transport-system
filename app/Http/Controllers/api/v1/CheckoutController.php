<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\SchoolTrip;
use App\Models\SchoolTripPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function getEncoding(string $consumerKey, string $consumerSecret)
    {
        
        $fullString = $consumerKey.":".$consumerSecret;
        $encoded = base64_encode($fullString);
        return $encoded;
    }

    public function getToken(Request $request)
    {
        $json = json_decode($request->getContent(), true);

        $schooltrip = SchoolTrip::find($json['schooltrip_id']);

        $phone_number = $json["phone_number"];

        $student_id = $json["student_id"];

        $mpesa = DB::table('mpesa_settings')->find(1);

        $key = $mpesa->key ?? '2TjuNLzJC1jG0GyVPUth27059aGswkpC';
        $secret = $mpesa->secret ?? 'uBDYJdsHRRGfvOti';
        $shcode = $mpesa->shortcode ?? 8239368;

        $encodedConsumer = $this->getEncoding($key, $secret);
        //get the token
        $ch = curl_init('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic $encodedConsumer"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        $dec = json_decode($response, true);

        return $this->charge($dec['access_token'], $schooltrip->price, $schooltrip->id, $phone_number, $student_id, $shcode);

    }

    public function charge($token, $amount, $schooltrip_id, $phone, $student_id, $shcode)
    {
        $ShortCode = $shcode;
        $CommandID = "CustomerPayBillOnline";
        $Amount = $amount;
        $Msisdn = (string) $phone;
        $BillRefNumber = '12345678';
     
        $user_id = auth('api')->user();

        $payload = array(
            'ShortCode' => $ShortCode,
            'CommandID' => $CommandID,
            'Amount' => $Amount,
            'Msisdn' => $Msisdn,
            'BillRefNumber' => $BillRefNumber
        );

        $data = json_encode($payload);
        try {

            $ch = curl_init('https://sandbox.safaricom.co.ke/mpesa/c2b/v1/simulate');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $token",
                'Content-Type: application/json'
            ]);

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response  = curl_exec($ch);
            curl_close($ch);

            $response = json_decode($response, true);
           
            if (array_key_exists('errorMessage', $response)) {
                return response(0);
            } 

            if ($response['ResponseCode'] == 0) {
                return response(1);
            }

            return response(1);
        } catch (\Exception $e) 
        {
            return response($e->getMessage());
        }
    }


    public function updatePayemnt(Request $request)
    {
        $json = json_decode($request->getContent(), true);
        $trip = SchoolTrip::find($json['trip_id']);

        if (! $trip) {
            return abort(404, 'not found');
        }

        $store = DB::table('school_trip_payment_tables')->insert([
            "schooltrip_id" => $trip->id,
            "student_id" => $json['student_id'],
            "date" => date('Y-m-d')
        ]);

        if ($store) {
            return response(1);
        }
        return response(0);
    }
}
