<?php

namespace App\Notifications;

use App\Mail\AppLinks;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class Links extends Notification
{
    use Queueable;
    public $app_links;
    public $email;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($email)
    {
        $app_links = DB::table('app_links')->find(1);
        $this->email = $email;

        if($app_links) {
            $this->app_links = $app_links;
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = $this->app_links;
        return (new AppLinks)->to($this->email)->view('links',["links" => $url]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
