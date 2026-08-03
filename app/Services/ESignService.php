<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\ESign;
use App\Models\ESignTemplate;
use App\Notifications\ESignNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ESignService
{
    /**
     * The document number generator service.
     */
    protected DocumentNumberService $numberService;

    /**
     * Create a new ESignService instance.
     */
    public function __construct(DocumentNumberService $numberService)
    {
        $this->numberService = $numberService;
    }

    /**
     * Store a new draft document.
     *
     * Proses:
     * 1. Template dipilih → isi template disalin ke field `content`
     * 2. Judul surat diisi manual oleh user
     * 3. Nomor surat tidak digenerate — diisi nanti pada tahap approval
     * 4. Status awal: draft
     *
     * @param array $data Validated data dari StoreESignRequest
     * @return \App\Models\ESign
     */
    public function storeDraft(array $data): ESign
    {
        return DB::transaction(function () use ($data) {
            // Snapshot template: copy HTML content dari template ke draft
            $templateId = null;
            if (!empty($data['template_id'])) {
                $template = ESignTemplate::find($data['template_id']);
                if ($template) {
                    $templateId = $template->id;
                }
            }

            // Gunakan content dari form (sudah di-render dengan placeholder diganti)
            // Fallback ke raw template content jika tidak ada content dari form
            $content = $data['content'] ?? null;
            if (empty($content) && !empty($data['template_id'])) {
                $template = ESignTemplate::find($data['template_id']);
                if ($template) {
                    $content = $template->content;
                }
            }

            // document_name = judul surat (display purposes)
            $documentName = $data['title'] ?? $data['document_name'] ?? 'Draft Surat';

            // Handle employee_id from multi-select (array) or single select
            $employeeId = $data['employee_id'] ?? null;
            if (is_array($employeeId)) {
                $employeeId = count($employeeId) > 0 ? (int) $employeeId[0] : null;
            }

            $eSign = ESign::create([
                'employee_id'      => $employeeId,
                'employee1_signee_id' => $data['employee1_signee_id'] ?? null,
                'employee2_signee_id' => $data['employee2_signee_id'] ?? null,
                'employee3_signee_id' => $data['employee3_signee_id'] ?? null,
                'letter_type_id'   => $data['letter_type_id'] ?? null,
                'template_id'      => $templateId,
                'nomor_surat'      => null, // belum digenerate
                'document_name'    => $documentName,
                'title'            => $data['title'] ?? null,
                'content'          => $content,
                'document_type'    => 'contract',
                'jenis_surat_slug' => $data['jenis_surat_slug'],
                'description'      => $data['description'] ?? null,
                'document_path'    => '',
                'file_name'        => '',
                'file_size'        => 0,
                'status'           => ESign::STATUS_DRAFT,
                'created_by'       => Auth::id(),
                'upload_date'      => Carbon::now(),
                'tanggal_mulai'    => $data['tanggal_mulai'],
                'tanggal_akhir'    => $data['tanggal_akhir'] ?? null,
            ]);

            return $eSign;
        });
    }

    /**
     * Update an existing draft document.
     *
     * Only documents with status 'draft' can be updated.
     * Fields that cannot be changed: nomor_surat, jenis_surat_slug, status, template_id.
     *
     * @param \App\Models\ESign $eSign
     * @param array $data Validated data dari UpdateESignRequest
     * @return \App\Models\ESign
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function updateDraft(ESign $eSign, array $data): ESign
    {
        // Only draft documents can be edited
        if (!$eSign->isDraft()) {
            abort(403, 'Hanya dokumen dengan status Draft yang dapat diedit.');
        }

        return DB::transaction(function () use ($eSign, $data) {
            $employeeId = $data['employee_id'] ?? null;
            if (is_array($employeeId)) {
                $employeeId = count($employeeId) > 0 ? (int) $employeeId[0] : $eSign->employee_id;
            }

            $eSign->update([
                'employee_id'   => $employeeId,
                'employee1_signee_id' => $data['employee1_signee_id'] ?? $eSign->employee1_signee_id,
                'employee2_signee_id' => $data['employee2_signee_id'] ?? $eSign->employee2_signee_id,
                'employee3_signee_id' => $data['employee3_signee_id'] ?? $eSign->employee3_signee_id,
                'title'         => $data['title'] ?? $eSign->title,
                'document_name' => $data['title'] ?? $eSign->document_name,
                'content'       => $data['content'] ?? $eSign->content,
                'description'   => $data['description'] ?? null,
                'tanggal_mulai' => $data['tanggal_mulai'],
                'tanggal_akhir' => $data['tanggal_akhir'] ?? null,
            ]);

            return $eSign->fresh();
        });
    }

    /**
     * Employee signs (approves) a document at the current sign level.
     *
     * Flow:
     * - Jika status sign_1 → catat employee1_signed_at, kirim email ke Sign 2, lanjut ke sign_2
     * - Jika status sign_2 → catat employee2_signed_at, kirim email ke Sign 3, lanjut ke sign_3
     * - Jika status sign_3 → catat employee3_signed_at, selesai → completed
     * - Jika employee yang login bukan gilirannya, tolak.
     */
    public function approveByEmployee(ESign $eSign, int $employeeId): ESign
    {
        if (!$eSign->canBeResponded($employeeId)) {
            abort(403, 'Anda tidak berhak menandatangani surat ini saat ini.');
        }

        return DB::transaction(function () use ($eSign) {
            $now = now();
            $currentLevel = $eSign->getCurrentSignLevel();

            $updateData = [];

            if ($currentLevel === 1) {
                $updateData['employee1_signed_at'] = $now;
                // Jika ada signee2, lanjut ke sign_2. Jika tidak, langsung completed.
                $updateData['status'] = $eSign->employee2_signee_id
                    ? ESign::STATUS_SIGN_2
                    : ESign::STATUS_COMPLETED;
            } elseif ($currentLevel === 2) {
                $updateData['employee2_signed_at'] = $now;
                $updateData['status'] = $eSign->employee3_signee_id
                    ? ESign::STATUS_SIGN_3
                    : ESign::STATUS_COMPLETED;
            } elseif ($currentLevel === 3) {
                $updateData['employee3_signed_at'] = $now;
                $updateData['status'] = ESign::STATUS_COMPLETED;
            }

            $eSign->update($updateData);
            $eSign = $eSign->fresh();

            // Kirim email ke sign berikutnya jika ada
            $nextLevel = $eSign->getCurrentSignLevel();
            if ($nextLevel > 0 && $nextLevel > $currentLevel) {
                $this->sendSignNotification($eSign, $nextLevel);
            }

            return $eSign;
        });
    }

    /**
     * Send email notification to the signee at the given level.
     */
    private function sendSignNotification(ESign $eSign, int $signLevel): void
    {
        $signeeId = match($signLevel) {
            1 => $eSign->employee1_signee_id,
            2 => $eSign->employee2_signee_id,
            3 => $eSign->employee3_signee_id,
            default => null,
        };

        if (!$signeeId) return;

        $signee = Employee::find($signeeId);
        if (!$signee || !$signee->email) return;

        Notification::route('mail', $signee->email)
            ->notify(new ESignNotification($eSign, $signee, $signLevel));
    }

    /**
     * Send a draft document to the first employee (Sign 1) for signing.
     * Only documents with status 'draft' can be sent.
     * Generates nomor_surat automatically with format: PREFIX/YEAR/NUMBER.
     * Updates status to 'sign_1' and sends email notification to Sign 1.
     */
    public function sendToEmployee(ESign $eSign): ESign
    {
        if (!$eSign->canBeSent()) {
            abort(403, 'Hanya dokumen dengan status Draft yang dapat dikirim ke Employee.');
        }

        return DB::transaction(function () use ($eSign) {
            // Generate nomor surat otomatis
            $nomorSurat = $this->numberService->generateNextNumber($eSign->jenis_surat_slug);

            $eSign->update([
                'nomor_surat' => $nomorSurat,
                'status' => ESign::STATUS_SIGN_1,
            ]);

            $eSign = $eSign->fresh();

            // Kirim email ke Sign 1
            $this->sendSignNotification($eSign, 1);

            return $eSign;
        });
    }

    /**
     * Employee rejects a document (optional — not used in current flow).
     */
    public function rejectByEmployee(ESign $eSign): ESign
    {
        if (!$eSign->canBeResponded($eSign->getCurrentSigneeId() ?? 0)) {
            abort(403, 'Dokumen ini tidak bisa di-reject.');
        }

        return DB::transaction(function () use ($eSign) {
            $eSign->update(['status' => ESign::STATUS_REJECTED_EMPLOYEE]);
            return $eSign->fresh();
        });
    }
}
