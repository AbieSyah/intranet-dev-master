<?php

namespace App\Notifications;

use App\Models\ESign;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ESignRecipientNotification extends Notification
{
    use Queueable;

    protected ESign $eSign;
    protected Employee $recipient;

    /**
     * Create a new notification instance.
     */
    public function __construct(ESign $eSign, Employee $recipient)
    {
        $this->eSign = $eSign;
        $this->recipient = $recipient;
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
        $jenisSurat = $this->eSign->jenis_surat_label;
        $nomorSurat = $this->eSign->nomor_surat ?? '-';
        $pengirim = $this->eSign->creator->employee->fullname ?? $this->eSign->creator->name ?? 'Sistem';

        $url = route('e-sign.profile-index', ['esign' => $this->eSign->id, 'tab' => 'confirm']);

        return (new MailMessage)
            ->subject("E-Sign: {$jenisSurat} — Mohon Konfirmasi Telah Membaca")
            ->greeting("Yth. {$this->recipient->fullname},")
            ->line('Surat berikut telah ditandatangani dan ditujukan kepada Anda:')
            ->line("**Jenis Surat:** {$jenisSurat}")
            ->line("**Nomor Surat:** {$nomorSurat}")
            ->line("**Pengirim:** {$pengirim}")
            ->line("**Tanggal:** {$this->eSign->tanggal_mulai_formatted}")
            ->line('Silakan baca suratnya, lalu konfirmasikan bahwa Anda telah menerima & membacanya.')
            ->action('Lihat & Konfirmasi', $url)
            ->line('Terima kasih.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'e_sign_id' => $this->eSign->id,
        ];
    }
}