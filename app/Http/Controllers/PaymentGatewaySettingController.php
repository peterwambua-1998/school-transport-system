<?php

namespace App\Http\Controllers;

use App\Models\PaymentGatewaySetting;
use Illuminate\Http\Request;

class PaymentGatewaySettingController extends Controller
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
        $request->validate([
            'stripe_public' => 'required',
            'stripe_private' => 'required'
        ]);

        $isPaySettings = PaymentGatewaySetting::find(1);

        if (! $isPaySettings) {
            $paymentGatewaySetting  = new PaymentGatewaySetting();
            $paymentGatewaySetting->public_key = $request->stripe_public;
            $paymentGatewaySetting->private_key = $request->stripe_private;

            if ($paymentGatewaySetting->save()) {
                return redirect()->back()->with('success', 'PaymentGateway Settings stored successfully');
            }
        } else {
            $paymentGatewaySetting  = PaymentGatewaySetting::find(1);
            $paymentGatewaySetting->public_key = $request->stripe_public;
            $paymentGatewaySetting->private_key = $request->stripe_private;

            if ($paymentGatewaySetting->update()) {
                return redirect()->back()->with('success', 'PaymentGateway Settings stored successfully');
            }
        }
        
        return redirect()->back()->with('unsuccess', 'Sytem error please try again later');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PaymentGatewaySetting  $paymentGatewaySetting
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentGatewaySetting $paymentGatewaySetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PaymentGatewaySetting  $paymentGatewaySetting
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentGatewaySetting $paymentGatewaySetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PaymentGatewaySetting  $paymentGatewaySetting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentGatewaySetting $paymentGatewaySetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PaymentGatewaySetting  $paymentGatewaySetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentGatewaySetting $paymentGatewaySetting)
    {
        //
    }
}
