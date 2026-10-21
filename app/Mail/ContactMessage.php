<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    /** @param array{name: string, email: string, phone: ?string, topic: string, message: string} $details */
    public function __construct(public array $details)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Website contact: '.ucfirst($this->details['topic']),
            replyTo: [new Address($this->details['email'], $this->details['name'])],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.contact-message',
        );
    }
}