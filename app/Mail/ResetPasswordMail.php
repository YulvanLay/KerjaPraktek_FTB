<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetUrl;
    public $username;

    public function __construct($resetUrl, $username)
    {
        $this->resetUrl = $resetUrl;
        $this->username = $username;
    }

    public function build()
    {
        return $this
            ->subject('Reset Password')
            ->view('mail.reset-password-mail');
    }
}