<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */

     public $serviceinfo;
     public $payment;
    public function __construct($serviceinfo, $payment)
    {
        $this->serviceinfo = $serviceinfo;
        $this->payment = $payment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(){
        return [



            'service info' => $this->serviceinfo,
            'payment type'=> $this->payment,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
