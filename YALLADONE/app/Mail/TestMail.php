<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $mailData;
    public $senderEmail;
    public function __construct($mailData,$senderEmail)
    {
        $this->mailData = $mailData;
        $this->senderEmail = $senderEmail;

    }

    public function build()
    {
        return $this->view('test')
                    ->from  ($this->senderEmail, config('app.name'))
                    ->subject('User Feedback ')
                    ->with('mailData', $this->mailData);
    }


}
