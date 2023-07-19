<?php

namespace App\Http\Controllers;

use App\Models\DepatureChecklist;
use App\Models\Invoice;
use App\Models\PaymentGatewaySetting;
use App\Models\Receipt;
use App\Models\ReceiptSchoolTrip;
use App\Models\SchoolTrip;
use App\Models\SchoolTripPaymentTable;
use App\Models\Student;
use App\Models\User;
use App\Notifications\InvoicePaid;




use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $invoice = Invoice::find($request->inv);

  
        $parent = Auth::user();

        $pNofitications = User::find($parent->id)->unreadNotifications;

        

        $numOfNotifications = count($pNofitications);

        

        return view('checkout.index')->with([
            'invoice'=> $invoice,
            'pNofitications' => $pNofitications,
            'numOfNotifications' => $numOfNotifications
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $jsonStr = file_get_contents('php://input');
        $jsonObj = json_decode($jsonStr);

        $balance = 0;

        if ($jsonObj->items[0]->name == 'invoice') {
            # code...
       
            $inv = $jsonObj->items[0]->id;

            $invoice = Invoice::find($inv);

            $amtReceipt = 0;

            foreach ($invoice->receipt as $receipt) {
                    $amtReceipt += $receipt->amount;
            }
            $invAmt = $invoice->amount;

            $balance = $invAmt - $amtReceipt;

        }

        if ($jsonObj->items[0]->name == 'schooltrip') {
            # code...
       
            $inv = $jsonObj->items[0]->id;

            $invoice = SchoolTrip::find($inv);

            

            $balance = $invoice->price - 0;

        }

            $paySettings = PaymentGatewaySetting::find(1);

            if (! $paySettings) {
                $key = 'sk_test_51HY26JJsL6OGbcr6yeTGu2BgSu3gNBjBQKE1xH65u9cLiFTiTp4CZ6ouVF5PuLIujruIeAECG3AeOROaqUydnRjv00sM6S6v1W';
            } else {
                $key = $paySettings->private_key; 
            }

        
            
            Stripe::setApiKey($key);



            function calculateOrderAmount($balance): int {
                // Replace this constant with a calculation of the order's amount
                // Calculate the order total on the server to prevent
                // people from directly manipulating the amount on the client
                
                return $balance / 0.01;

                
            }

            header('Content-Type: application/json');

            try {
                // retrieve JSON from POST body
                

                // Create a PaymentIntent with amount and currency
                $paymentIntent = PaymentIntent::create([
                    'customer' => $request->user,
                    'amount' => calculateOrderAmount($balance),
                    'currency' => 'usd',
                    'automatic_payment_methods' => [
                        'enabled' => true,
                    ],
                    'setup_future_usage' => 'on_session',
                ]);

                
                $output = [
                    'clientSecret' => $paymentIntent->client_secret,
                    'amount' => $paymentIntent->amount,
                    
                ];

                

                return json_encode($output);
            } catch (\Exception $e) {
                http_response_code(500);
                return json_encode(['error' => $e->getMessage()]);
            }
        


        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        dd($request);
        return true;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function myupdate($id)
    {
        $invoice = Invoice::find($id);

        $invoice->status = 'paid';
        $invoice->date_paid = date('Y-m-d');
        $invoice->update();


        $users = User::where('user_type', 'LIKE', 'office staff')->get();

        Notification::send($users, new 
        
        ($invoice));
        
        return redirect()->route('phome')->with('success', 'payment was successful');
    }


    public function getKey()
    {
        $settings = PaymentGatewaySetting::find(1);
        

        if (! $settings) {
            $publicKey = 'pk_test_51HY26JJsL6OGbcr6YqUofolvSJLapbmQ2x13RDvUx4wIEtEB8gjKwyUaJTB2qmEI3dXJCXjRJsiv2WfVTYWl1u6K00OqaVC8Qm';
        } else {
            $publicKey = $settings->public_key;
        }
        

        return Response($publicKey);
    }


    public function getKeytwo()
    {
        $settings = PaymentGatewaySetting::find(1);
        

        if (! $settings) {
            $publicKey = 'pk_test_51HY26JJsL6OGbcr6YqUofolvSJLapbmQ2x13RDvUx4wIEtEB8gjKwyUaJTB2qmEI3dXJCXjRJsiv2WfVTYWl1u6K00OqaVC8Qm';
        } else {
            $publicKey = $settings->public_key;
        }
        

        return Response($publicKey);
    }


    public function pCheckoutTrip(Request $request)
    {
        $trip = SchoolTrip::find($request->inv);
        $student = Student::find($request->student_id);
        $parent = Auth::user();
        $pNofitications = User::find($parent->id)->unreadNotifications;
        $numOfNotifications = count($pNofitications);

        $date = date('Y-m-d');

        $paymentTable = new SchoolTripPaymentTable();
        $paymentTable->schooltrip_id = $trip->id;
        $paymentTable->student_id = $student->id;
        $paymentTable->date = $date;
        $paymentTable->save();

        return view('parentlogin.tripcheckout')->with([
            'invoice'=> $trip,
            'pNofitications' => $pNofitications,
            'student' => $student,
            'numOfNotifications' => $numOfNotifications
        ]);
    }

    public function storeTrip(Request $request)
    {
        $inv = $request->items;

        

        $invoice = SchoolTrip::find($inv); 

        return response($invoice);
       

        $balance = $invoice->price;

        $paySettings = PaymentGatewaySetting::find(1);

       
        if (! $paySettings) {
            $key = 'sk_test_51HY26JJsL6OGbcr6yeTGu2BgSu3gNBjBQKE1xH65u9cLiFTiTp4CZ6ouVF5PuLIujruIeAECG3AeOROaqUydnRjv00sM6S6v1W';
        } else {
            $key = $paySettings->private_key; 
        }
        
        Stripe::setApiKey($key);



        function calculateOrderAmounts($balances): int {
            // Replace this constant with a calculation of the order's amount
            // Calculate the order total on the server to prevent
            // people from directly manipulating the amount on the client
            
            return $balances;
        }
        
        header('Content-Type: application/json');

        try {
            // retrieve JSON from POST body
            $jsonStrs = file_get_contents('php://input');
            $jsonObj = json_decode($jsonStrs);

            // Create a PaymentIntent with amount and currency
            $paymentIntents = PaymentIntent::create([
                
                'amount' => calculateOrderAmounts($balance),
                'currency' => 'usd',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

           

            
            $outputs = [
                'clientSecret' => $paymentIntents->client_secret,
                'amount' => $paymentIntents->amount
            ];

            

            return json_encode($outputs);
        } catch (\Exception $e) {
            http_response_code(500);
            return json_encode(['error' => $e->getMessage()]);
        }
    }


    public function myupdateTrip($id, $student_id)
    {
        $trip = SchoolTrip::find($id);

        $student = Student::find($student_id);

        $invoice = new ReceiptSchoolTrip();
        $invoice->trip_id = $trip->id;
        $invoice->student_id = $student->id;
        $invoice->amount = $trip->price;
        $invoice->save();


        $depature = new DepatureChecklist();
        $depature->schooltrip_id = $trip->id;
        $depature->student_id = $student->id;
        $depature->grade = $student->grade;
        $depature->attendance = 'absent';
        if($depature->save()) {
            return redirect()->route('parent_gettrips')->with('success', 'Trip payment was successful');
        };

        return redirect()->route('parent_gettrips')->with('unsuccess', 'Error occured consult school');
        

    }
}
