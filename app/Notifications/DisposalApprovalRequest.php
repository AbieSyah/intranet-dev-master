<?php

namespace App\Notifications;

use App\Models\AssetDisposal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisposalApprovalRequest extends Notification
{
    use Queueable;

    public $transaction;
    public $status;

    const REVIEW = 0;
    const REVISION = 1;
    const APPROVED = 2;


    /**
     * Create a new notification instance.
     */
    public function __construct(AssetDisposal $transaction, $status = DisposalApprovalRequest::REVIEW) // write "revision" in order to send revision email notification
    {
        $this->transaction = $transaction;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable)
    {
        $isRevision = $this->status === DisposalApprovalRequest::REVISION;
        $isReview = $this->status === DisposalApprovalRequest::REVIEW;

        // 1. Determine the URL only if needed
        $url = null;
        if ($isRevision) {
            $url = route('asset-disposal.revision', encrypt($this->transaction->id));
        } elseif ($isReview) {
            $url = route('asset-disposal.review', ['id' => encrypt($this->transaction->id)]);
        }

        // 2. Determine the Subject and Message line
        $subject = $isRevision 
            ? "Revision Required: #{$this->transaction->transaction_number}" 
            : ($isReview ? "Approval Required: #{$this->transaction->transaction_number}" : "Disposal Approved: #{$this->transaction->transaction_number}");

        $line = match ($this->status) {
            DisposalApprovalRequest::REVISION => "Your disposal request has been sent back for revision.",
            DisposalApprovalRequest::REVIEW   => "A new disposal request requires your approval.",
            default => "Your disposal has been approved. Waiting for the buyer to sign the disposal.",
        };

        // 3. Build the MailMessage
        $message = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello, " . $notifiable->employee->fullname)
            ->line($line);

        // 4. Add the button ONLY if it's Revision or Review
        if ($url) {
            $message->action($isRevision ? 'Fix Revision' : 'Review & Approve', $url);
        }

        return $message
            ->line("Transaction Number: " . $this->transaction->transaction_number)
            ->line("Please complete this action promptly.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
