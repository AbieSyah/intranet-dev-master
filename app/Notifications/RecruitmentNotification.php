<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecruitmentNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public $candidateData;
    public $positionName;
    public $type;
    public $extraData;

    public function __construct(
        array $candidateData, 
        string $positionName, 
        string $type,
        array $extraData = []
    )
    {
        $this->candidateData = $candidateData;
        $this->positionName = $positionName;
        $this->type = $type;
        $this->extraData = $extraData;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $view = '';
        $subject = '';
        switch ($this->type) {
            case 'submit':
                $subject = 'Recruitment Confirmation';
                $view = 'pages.hrd.recruitment.candidate.mail.submit';
                break;
            case 'schedule':
                $subject = 'Recruitment Invitation';
                $view = 'pages.hrd.recruitment.candidate.mail.schedule';
                break;
            default:
                $subject = 'Recruitment Confirmation';
                $view = 'pages.hrd.recruitment.candidate.mail.submit';
                break;
        }
        $data = array_merge([
            'title' => $subject,
            'candidate' => $this->candidateData,
            'position_name' => $this->positionName,
        ], $this->extraData);
        return (new MailMessage)
            ->subject($subject . ' - PT. Hisamitsu Pharma Indonesia')
            ->view($view, $data);
    }
}