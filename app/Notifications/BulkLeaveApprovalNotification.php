<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BulkLeaveApprovalNotification extends Notification
{
    use Queueable;
    private $request;

    /**
     * Create a new notification instance.
     */
    public function __construct($request)
    {
        $this->request = $request;

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

    // public function toMail($notifiable)
    // {
    //     $mail = (new MailMessage)
    //         ->subject($this->request['subject'])
    //         ->greeting($this->request['greeting'])
    //         ->line('Ada beberapa pengajuan cuti yang menunggu approval Anda:');

    //     foreach ($this->request['requests'] as $req) {
    //         $mail->line(
    //             "{$req['employee_name']} | {$req['type']} | {$req['start_date']} s/d {$req['end_date']}"
    //         );
    //     }

    //     if (!empty($this->request['actionText']) && !empty($this->request['actionURL'])) {
    //         $mail->action($this->request['actionText'], $this->request['actionURL']);
    //     }

    //     $mail->line($this->request['thanks']);

    //     return $mail;
    // }
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject($this->request['subject'])
            ->greeting($this->request['greeting'])
            ->line($this->request['intro'] ?? 'Ada pengajuan yang menunggu approval Anda:');

        foreach ($this->request['requests'] as $req) {
            $mail->line($req['text']);
        }

        if (!empty($this->request['actionText']) && !empty($this->request['actionURL'])) {
            $mail->action($this->request['actionText'], $this->request['actionURL']);
        }

        $mail->line($this->request['thanks']);

        return $mail;
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
