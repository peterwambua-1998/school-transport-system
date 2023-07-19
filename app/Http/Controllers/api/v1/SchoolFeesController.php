<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Traits\PaypalTrait;
use App\Http\Controllers\Controller;
use App\Models\FeeEntry;
use App\Models\FeePayment;
use App\Models\SchoolFees;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\StudentFeeDetails;
use App\Models\User;
use App\Notifications\GeneratedPassword;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use stdClass;

class SchoolFeesController extends Controller
{

    use PaypalTrait;
    /**
     * @param student
     * @return Illuminate\Http\Response
     */
    public function paidFess($id)
    {
        $student = Student::find($id);

        if (! $student) {
            return abort(404, 'not found');
        }

        $paidFees = SchoolFees::where('grade','=',$student->grade)->orderBy('created_at','desc')->where('active','=', 1)->get();

        $finalArr = [];

        foreach ($paidFees as $paidFee) {
            $student_fee = StudentFee::where('fee_id','=',$paidFee->id)->first();
            $details = StudentFeeDetails::where('student_fees_id','=', $student_fee->id)->get();
            $feePayments = FeePayment::where('school_fees_id','=',$student_fee->id)->get();
            $amt_paid = 0;
            foreach ($feePayments as $key => $det) {
                $amt_paid += $det->amount_paid;
            }
            $balance = $student_fee->amount - $amt_paid;
            $student_fee->balance = $balance;
            $student_fee->details = $details;
            $arr = ["fee_statement" => [
                "fee_structure" => $student_fee,
                "fee_payments" => $feePayments
            ]];
            array_push($finalArr, $arr);
        }

        return response($finalArr);
    }

    /**
     * @return Illuminate\Http\Response
     */
    public function getUnpaidFess($id)
    {
        $student = Student::find($id);

        if (! $student) {
            return abort(404, 'not found');
        }

        $unpaidFees = SchoolFees::where('student','=',$student->id)->where('status','=', 0)->get();

        return response($unpaidFees);
    }

    /**
     * MPESA
     * pay fee
     */
    public function getEncoding(string $consumerKey, string $consumerSecret)
    {
        $fullString = $consumerKey.":".$consumerSecret;
        $encoded = base64_encode($fullString);
        return $encoded;
    }

    public function getToken(Request $request)
    {
        //define('CALLBACK_URL', route('/school-fees-refences'));

        $json = json_decode($request->getContent(), true);

        $fees = StudentFee::find($json['fees_id']);
        $phone_number = $json["phone_number"];
        $student_id = $json["student_id"];
        $amount_paid = $json["amount"];

        $mpesa = DB::table('mpesa_settings')->find(1);

        $key = '2TjuNLzJC1jG0GyVPUth27059aGswkpC';
        $secret = 'uBDYJdsHRRGfvOti';
        $shcode = 600995;

        $encodedConsumer = $this->getEncoding($key, $secret);
        //get the token
        $ch = curl_init('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic $encodedConsumer"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        $dec = json_decode($response, true);

        return $this->charge($dec['access_token'], $amount_paid, $fees->id, $phone_number, $student_id, $shcode, $fees->invoice_num);

    }

    public function charge($token, $amount, $fees_id, $phone, $student_id, $shcode, $ref_num)
    {

        $ShortCode = $shcode;
        $CommandID = "CustomerPayBillOnline";
        $Amount = $amount;
        $Msisdn = (string) $phone;
        $BillRefNumber = 40404;
     
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

            return response(['res' => $response]);
           
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

    /**
     * update fee payemnt
     */
    public function updatePayemnt(Request $request)
    {
        $json = json_decode($request->getContent(), true);
        $fees = SchoolFees::find($json['fees_id']);
        $student = Student::find($json['student_id']);

        $feePayment = new FeePayment();
        $feePayment->receipt_number = date('YmdHis');
        $feePayment->school_fees_id = $fees->id;
        $feePayment->student = $student->id;
        $feePayment->amount_paid = $json['amount'];
        $feePayment->payment_method = 'mpesa';
        //balance
        $balance = $fees->amount - $json['amount'];

        if ($balance <= 0) {
            $fees->status = 1;
            $fees->update();
        }

        $feePayment->balance = $balance;
        $feePayment->date_paid = date('Y-m-d');
        if($feePayment->save()){
            return response(1);
        };
        
        return response(0);
    }

    /**
     * to do tomorrow
     */
    public function storeMpesaPaymentRefereces()
    {
        
    }

    /**
     * PAYPAL
     */

    public function generateToken()
    {

        $client_id = $this->getKeys()['client_id'];
        $app_secret = $this->getKeys()['app_secret'];
        $sandbox_url = $this->urls()['sandbox'];


        $auth = base64_encode("$client_id:$app_secret");

        $ch = curl_init("$sandbox_url/v1/oauth2/token");
        curl_setopt($ch, CURLOPT_USERPWD, $auth);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic $auth"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        $dec = json_decode($response, true);


        return $dec;
    }

    public function createOrder(Request $request)
    {

        $response = $this->generateToken();

        $client_id = $this->getKeys()['client_id'];
        $app_secret = $this->getKeys()['app_secret'];
        $sandbox_url = $this->urls()['sandbox'];

        $json = json_decode($request->getContent(), true);

        $amount = $json['amount'] * 0.0073;


        if ($response["access_token"]) {
            $token = $response["access_token"];
            $headers = array(
                'Content-Type: application/json',
                "Authorization: Bearer $token"
            );

           
            $jsonPayload = array(
                'intent' => 'CAPTURE',
                'purchase_units' => array(
                    array(
                        'amount' => array(
                            'currency_code' => 'USD',
                            'value' => "$amount.00"
                        )
                    )
                )
            );
        
            $ch = curl_init("$sandbox_url/v2/checkout/orders");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($jsonPayload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $resp = curl_exec($ch);
            curl_close($ch);
    
            $data = json_decode($resp, true);

            return response($data);
        }
        return response(0);
    }

    public function capturePayment(Request $request)
    {
        $sandbox_url = $this->urls()['sandbox'];

        $json =  json_decode($request->getContent(), true)['orderID'];

        $response = $this->generateToken();

        if ($response['access_token']) {
            $token = $response["access_token"];
            $headers = array(
                'Content-Type: application/json',
                "Authorization: Bearer $token"
            );

            $ch = curl_init("$sandbox_url/v2/checkout/orders/$json/capture");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $res = curl_exec($ch);
            curl_close($ch);
    
            $data = json_decode($res, true);
            return response(["resp" => $data]);
        }
        return response(0);
    }

    public function paypalPage()
    {
        return view('welcome');
    }

    
    public function test(Request $request)
    {
        $json = json_decode($request->getContent(), true);
        return  response($json['orderID']);
    }

    
}
