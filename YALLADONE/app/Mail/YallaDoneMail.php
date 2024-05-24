<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class YallaDoneMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $mailData;
    
    public function __construct($mailData)
    {
        $this->mailData=$mailData;

    }

    public function build()
    {
        return $this->view('test1')
                    ->from  ('YallaDone@gmail.com', config('app.name'))
                    ->subject('User Welcome ')
                    ->with('mailData', $this->mailData);
    }


}
