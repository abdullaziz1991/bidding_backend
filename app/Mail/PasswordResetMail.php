<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $resetUrl;

    public function __construct($user, $resetUrl)
    {
        $this->user = $user;
        $this->resetUrl = $resetUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'إعادة تعيين كلمة المرور',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.passwordReset', // ← صحح اسم الـ view هنا
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
