<?php

namespace App\Mail;

use App\Models\AssetDisposal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DisposalBuyerConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $url;
    public $transaction;

    /**
     * Create a new message instance.
     */
    public function __construct(AssetDisposal $transaction, $url)
    {
        $this->url = $url;
        $this->transaction = $transaction;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Buyer Confirmation',
        );
    }

    // app/Mail/PurchaseConfirmation.php
    public function build()
    {
        return $this->subject('Final Validation: Confirm Your Purchase')
            ->markdown('emails.disposal-buyer-confirmation', [
                'url' => $this->url,
                'transaction' => $this->transaction
            ]);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
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
