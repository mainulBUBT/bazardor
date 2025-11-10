<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The application name.
     */
    public string $appName;

    /**
     * The timestamp the email was generated.
     */
    public string $sentAt;

    public function __construct(string $appName)
    {
        $this->appName = $appName;
        $this->sentAt = now()->format('Y-m-d H:i:s');
    }

    public function build(): self
    {
        return $this->subject(__('messages.Test Email Confirmation'))
            ->view('emails.test-mail')
            ->with([
                'appName' => $this->appName,
                'sentAt' => $this->sentAt,
            ]);
    }
}
