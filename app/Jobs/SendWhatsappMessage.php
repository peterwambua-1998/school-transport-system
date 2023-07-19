<?php

namespace App\Jobs;

use App\Models\Settings;
use App\Models\TermEvent;
use App\Models\User;
use App\Models\WhatsappSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client;

class SendWhatsappMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected TermEvent $event;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TermEvent $event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sid    = "AC15413682a25156ad5c83227d6d355b95";
        $token  = "f90223590431a80e8e15bd1d181e8486";
        $twilo = new Client($sid, $token);
        $parents = User::where('user_type', '=', 'parent')->get();
        $setting = Settings::find(1);
        $whatsapp = WhatsappSetting::find(1);
        $from = "whatsapp:$whatsapp->twilio_num" ?? 'whatsapp:+14155238886';
        $pickup = 'no';

        if($this->event->pickup) {
            $pickup = 'yes';
        }

        foreach ($parents as $parent) {
            $twilo->messages->create(
                "whatsapp:$parent->phone_num", // Text this number
                [
                  'from' => $from, // From a valid Twilio number
                  'body' => "👋 hellow from $setting->company_name There is a school event: {$this->event->name}, Date: {$this->event->start} Time from: {$this->event->start_time}, Requires pickup: $pickup. Regards $setting->company_name."
                ]
            );
        }
    }
}
