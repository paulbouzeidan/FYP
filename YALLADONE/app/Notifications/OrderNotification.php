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
     public $paymenttype;
    public function __construct($serviceinfo, $paymenttype)
    {
        $this->serviceinfo = $serviceinfo;
        $this->paymenttype = $paymenttype;
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
            'payment type'=> $this->paymenttype,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
