<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BackupDatabaseMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public string $file) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '備份資料庫',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.backup-database',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->file),
        ];
    }
}
