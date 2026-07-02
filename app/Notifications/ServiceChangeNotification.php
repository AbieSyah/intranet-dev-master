<?php

namespace App\Notifications;

use App\Models\ServiceChange;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ServiceChangeNotification extends Notification
{
    use Queueable;
    private $serviceChange;
    private $for;

    const FOR_APPROVER = 'approver';
    const FOR_PROPOSER = 'proposer';
    const FOR_OLD_APPROVER = 'old_approver';
    
    /**
     * Create a new notification instance.
     */
    public function __construct(ServiceChange $serviceChange, $for = null)
    {
        $this->serviceChange = $serviceChange;
        $this->for = $for;
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
    public function toMail(object $notifiable): MailMessage
    {
        $route = URL::signedRoute('service-change.public.index', [
            'id' => encrypt($this->serviceChange->id),
            'approverId' => encrypt($notifiable->employee->id)
        ]);
        return (new MailMessage)
            ->subject('New Change Management Proposal: ' . $this->serviceChange->change_no)
            ->greeting('Hello ' . $notifiable->employee->fullname . ',')
            ->line('A new service change has been proposed with the following details:')
            ->line('Change Number: ' . $this->serviceChange->change_no)
            ->line('Change Type: ' . ucfirst($this->serviceChange->change_type))
            ->line('Execution Plan: ' . $this->serviceChange->planned_start->format('Y-m-d H:i') . ' to ' . $this->serviceChange->planned_end->format('Y-m-d H:i'))
            ->line('IT Notice: ' . $this->serviceChange->it_notice)
            ->when($this->for !== self::FOR_OLD_APPROVER, function (MailMessage $message) use ($route) {
                $message->action('Review Change Proposal', $route);
            })
            ->line($this->for === self::FOR_OLD_APPROVER 
                ? 'You have been removed as the approver for this change.' 
                : 'Thank you for your attention to this matter.');
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
