<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FinalizedMail extends Mailable
{
    use Queueable, SerializesModels;
    private string $link;
    private string $name;
    private string | null $so_number;

    /**
     * Create a new message instance.
     */
    public function __construct(string $link, string $name, string | null $so_number)
    {
        $this->link = $link;
        $this->name = $name;
        $this->so_number = $so_number;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notice finalisasi Dokumen',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notice-finalized',
            with: [
                'link' => $this->link,
                'name' => $this->name,
                'so_number' => $this->so_number
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
