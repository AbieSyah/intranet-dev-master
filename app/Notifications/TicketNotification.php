<?php

namespace App\Notifications;

use App\Models\ServiceTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class TicketNotification extends Notification
{
    use Queueable;

    protected $ticket;
    protected $messageText;
    protected $targetRole;
    protected $approverId;

    public function __construct(ServiceTicket $ticket, $messageText, $targetRole, $approverId=null)
    {
        $this->ticket = $ticket;
        $this->messageText = $messageText;
        $this->targetRole = $targetRole;
        $this->approverId = $approverId;
    }

    // Gunakan channel 'mail' dan 'database' sekaligus
    public function via($notifiable)
    {
        return [
            'mail', 
            // 'database'
        ];
    }

    // Setting Konten Email
    public function toMail($notifiable)
    {
        // Generate URL Workspace terenkripsi
        // $url = route('service-management.workspace', [
        //     'id' => encrypt($this->ticket->id), 
        //     'role' => encrypt($this->targetRole),
        //     'approverId' => encrypt($this->approverId)
        // ]);
        if ($this->targetRole == ServiceTicket::ROLE_USER) {
            $url = route('service-desk.workspace', [
                'id' => encrypt($this->ticket->id), 
                'role' => encrypt(ServiceTicket::ROLE_USER)
            ]);
        } else if($this->targetRole == ServiceTicket::ROLE_CC) {
            $url = route('service-desk.workspace', [
                'id' => encrypt($this->ticket->id), 
                'role' => encrypt(ServiceTicket::ROLE_CC)
            ]);
        } else {
            $url = URL::signedRoute('service-ticket.approve-workspace', [
                'id' => encrypt($this->ticket->id), 
                'role' => encrypt($this->targetRole),
                'approverId' => encrypt($this->approverId)
            ]);
        }

        return (new MailMessage)
            ->subject($this->ticket->subject . ' - Ticket: ' . $this->ticket->no_ticket)
            ->greeting('Hi, ' . $notifiable->employee->fullname)
            ->line($this->messageText)
            ->line("Subject: " . $this->ticket->subject)
            ->line('Ticket Number: ' . $this->ticket->no_ticket)
            ->when($this->ticket->priority, function ($message) {
                return $message->line('Priority: ' . $this->ticket->priority->level);
            })
            ->line('IT Note: ' . $this->ticket->it_note)
            ->action('Open Workspace', $url) // Tombol redirect
            ->line('Thanks for using our IT service.');
    }

    // Setting Data untuk Database (Lonceng Notif)
    public function toArray($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'message' => $this->messageText,
            'url' => route('service-management.workspace', [
                'id' => encrypt($this->ticket->id), 
                'role' => encrypt($this->targetRole)
            ]),
        ];
    }
}