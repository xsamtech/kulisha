<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class ShortMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(private $token = null, private $ref = null, private $message = null)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        if ($this->token == null) {
            switch ($this->ref) {
                case 'invitation':
                    return new Envelope(
                        subject: __('miscellaneous.app_invitation.title'),
                    );
                    break;

                case 'payment':
                    return new Envelope(
                        subject: __('miscellaneous.bank_transaction_description'),
                    );
                    break;

                default:
                    return new Envelope(
                        subject: __('miscellaneous.app_invitation.title'),
                    );
                    break;
            }

        } else {
            return new Envelope(
                subject: 'Kulisha OTP',
            );
        }
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        if ($this->token == null) {
            return new Content(
                view: 'invitation', with: ['message' => $this->message]
            );

        } else {
            return new Content(
                view: 'otp-code', with: ['token' => $this->token]
            );
        }
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
