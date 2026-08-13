<?php

namespace App\Notifications;

use App\Models\ESign;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ESignNotification extends Notification
{
    use Queueable;

    protected ESign $eSign;
    protected Employee $signee;
    protected int $signLevel;

    /**
     * Create a new notification instance.
     */
    public function __construct(ESign $eSign, Employee $signee, int $signLevel)
    {
        $this->eSign = $eSign;
        $this->signee = $signee;
        $this->signLevel = $signLevel;
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
        // Arahkan ke halaman beserta query surat terkait.
        // Route e-sign.preview butuh permission 'e-sign.menu' (akses Pengelola),
        // sedangkan penerima surat hanya punya 'e-sign.profile'.
        $previewUrl = route('e-sign.profile-index', ['esign' => $this->eSign->id]);

        return (new MailMessage)
            ->subject("E-Sign: {$jenisSurat} — Mohon Tanda Tangan (Sign {$this->signLevel})")
            ->greeting("Yth. {$this->signee->fullname},")
            ->line("Anda menerima notifikasi untuk menandatangani dokumen berikut:")
            ->line("**Jenis Surat:** {$jenisSurat}")
            ->line("**Nomor Surat:** {$nomorSurat}")
            ->line("**Pengirim:** {$pengirim}")
            ->line("**Tanggal:** {$this->eSign->tanggal_mulai_formatted}")
            ->line("Anda ditetapkan sebagai **Sign {$this->signLevel}** pada dokumen ini.")
            ->action('Lihat & Tanda Tangan', $previewUrl)
            ->line("Silakan klik tombol di atas untuk melihat detail dan menandatangani dokumen.")
            ->line("Terima kasih.");
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
            'sign_level' => $this->signLevel,
        ];
    }
}
