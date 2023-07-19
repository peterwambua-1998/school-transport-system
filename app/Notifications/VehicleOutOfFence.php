<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleOutOfFence extends Notification
{
    use Queueable;

    public $vehicleTitle;
    public $vehiclePlate;
    public $driver;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($vehicleTitle, $vehiclePlate, $driver)
    {
        $this->vehicleTitle = $vehicleTitle;
        $this->vehiclePlate = $vehiclePlate;
        $this->driver = $driver;
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
            'body' => $this->vehicleTitle . ' ' . $this->vehiclePlate . ' is out of zone. Driver: ' . $this->driver .'.'
        ];
    }
}
