<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StopNotification extends Notification
{
    use Queueable;

    public $driver_name;
    public $vehicle_reg;
    public $vehicle_title;
    public $driver_phone;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($driver_name, $vehicle_reg,$vehicle_title, $driver_phone)
    {
        $this->driver_name = $driver_name;
        $this->vehicle_reg = $vehicle_reg;
        $this->vehicle_title = $vehicle_title;
        $this->driver_phone = $driver_phone;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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
            'body' => "Vehicle ($this->vehicle_title $this->vehicle_reg) has concluded its trip, Driver Name: $this->driver_name, Contact: $this->driver_phone."
            
        ];
    }
}
