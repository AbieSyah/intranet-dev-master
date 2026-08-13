<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\ESign;
use App\Models\ESignBatch;
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
     * The log service.
     */
    protected LogService $logService;

    /**
     * Create a new ESignService instance.
     */
    public function __construct(DocumentNumberService $numberService, LogService $logService)
    {
        $this->numberService = $numberService;
        $this->logService = $logService;
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
                'tanggal_mulai'    => $this->normalizeDate($data['tanggal_mulai'] ?? null),
                'tanggal_akhir'    => $this->normalizeDate($data['tanggal_akhir'] ?? null),
            ]);

            $this->logService->logCurrentUser(
                'insert',
                $this->logService->buildDescription(
                    'Buat Draft Surat pada menu E-Sign.',
                    [],
                    [
                        'Nomor Surat' => $eSign->nomor_surat ?? '-',
                        'Jenis Surat' => $eSign->jenis_surat_label,
                        'Judul' => $eSign->title ?? $eSign->document_name ?? '-',
                        'Status' => ($eSign->status ?? '') . '',
                    ]
                )
            );

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
            $before = [
                'Judul' => $eSign->title ?? $eSign->document_name ?? '-',
                'Tanggal Mulai' => $eSign->tanggal_mulai ? (string) $eSign->tanggal_mulai : '-',
                'Tanggal Akhir' => $eSign->tanggal_akhir ? (string) $eSign->tanggal_akhir : '-',
            ];

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
                'tanggal_mulai' => $this->normalizeDate($data['tanggal_mulai'] ?? null),
                'tanggal_akhir' => $this->normalizeDate($data['tanggal_akhir'] ?? null),
            ]);

            $eSign = $eSign->fresh();

            return $eSign;
        });
    }

    /**
     * Store a new multi-surat batch draft.
     *
     * Satu transaksi multi-surat menghasilkan N dokumen e_signs (satu per
     * penerima) yang dikelompokkan dalam 1 ESignBatch.
     *
     * Contract data:
     *   - recipients[] : array tiap elemen { employee_id, content, title? }
     *     content sudah di-render client-side dengan data karyawan tsb.
     *   - employee1_signee_id (opsional): HR/sign 1. Default: employee user login.
     *   - letter_type_id, template_id, jenis_surat_slug, description,
     *     tanggal_mulai, tanggal_akhir
     *
     * @param array $data
     * @return \App\Models\ESignBatch
     */
    public function storeDraftBatch(array $data): ESignBatch
    {
        return DB::transaction(function () use ($data) {
            $recipients = $data['recipients'] ?? [];
            if (empty($recipients)) {
                abort(422, 'Minimal harus ada 1 karyawan penerima untuk multi-surat.');
            }

            $batch = ESignBatch::create([
                'jenis_surat_slug' => $data['jenis_surat_slug'],
                'letter_type_id'   => $data['letter_type_id'] ?? null,
                'template_id'      => $data['template_id'] ?? null,
                'created_by'       => Auth::id(),
                'total_recipients' => count($recipients),
            ]);

            $sub = 1;
            foreach ($recipients as $r) {
                $employeeId = (int) ($r['employee_id'] ?? 0);
                if ($employeeId <= 0) {
                    continue;
                }

                $signees = $this->resolveBatchSignees($data, $employeeId);

                ESign::create([
                    'employee_id'         => $employeeId,
                    'employee1_signee_id' => $signees[1],
                    'employee2_signee_id' => $signees[2],
                    'employee3_signee_id' => $signees[3],
                    'letter_type_id'      => $data['letter_type_id'] ?? null,
                    'template_id'         => $data['template_id'] ?? null,
                    'batch_id'            => $batch->id,
                    'nomor_sub'           => $sub,
                    'nomor_surat'         => null,
                    'document_name'       => $r['title'] ?? $data['title'] ?? 'Draft Surat',
                    'title'               => $r['title'] ?? $data['title'] ?? null,
                    'content'             => $r['content'] ?? $data['content'] ?? '',
                    'document_type'       => 'contract',
                    'jenis_surat_slug'    => $data['jenis_surat_slug'],
                    'description'         => $data['description'] ?? null,
                    'document_path'       => '',
                    'file_name'           => '',
                    'file_size'           => 0,
                    'status'              => ESign::STATUS_DRAFT,
                    'created_by'          => Auth::id(),
                    'upload_date'         => Carbon::now(),
                    'tanggal_mulai'       => $this->normalizeDate($data['tanggal_mulai'] ?? null),
                    'tanggal_akhir'       => $this->normalizeDate($data['tanggal_akhir'] ?? null),
                ]);
                $sub++;
            }

            $this->logService->logCurrentUser(
                'insert',
                $this->logService->buildDescription(
                    'Buat Draft Multi-Surat pada menu E-Sign.',
                    [],
                    [
                        'Jenis Surat' => $batch->jenis_surat_slug,
                        'Jumlah Penerima' => (string) count($recipients),
                        'Status' => 'draft',
                    ]
                )
            );

            return $batch->fresh('documents');
        });
    }

    /**
     * Resolusi penandatangan per slot untuk satu penerima pada multi-surat.
     *
     * Slot penerima (dari template `recipient_sign`) diisi oleh penerima itu sendiri;
     * slot aktif lain diisi penandatangan tetap dari form (employee{slot}_signee_id);
     * slot tidak aktif bernilai null.
     *
     * @return array{1: int|null, 2: int|null, 3: int|null}
     */
    private function resolveBatchSignees(array $data, int $employeeId): array
    {
        $template = ESignTemplate::find($data['template_id'] ?? null);
        $slots    = $template ? $template->sign_slots : [1];
        $recSlot  = $template ? $template->recipient_sign : null;

        $signees = [1 => null, 2 => null, 3 => null];
        foreach ($slots as $slot) {
            if ($slot === $recSlot) {
                $signees[$slot] = $employeeId;
            } else {
                $signees[$slot] = $data["employee{$slot}_signee_id"] ?? null;
            }
        }

        return $signees;
    }

    /**
     * Update a multi-surat batch draft (masih status draft).
     *
     * Mensinkronkan daftar penerima & isi surat ke dokumen-dokumen batch.
     * Dokumen lama yang tidak ada lagi di recipients akan dihapus; penerima
     * baru akan dibuatkan dokumen.
     *
     * @param \App\Models\ESignBatch $batch
     * @param array $data (contract sama seperti storeDraftBatch)
     * @return \App\Models\ESignBatch
     */
    public function updateDraftBatch(ESignBatch $batch, array $data): ESignBatch
    {
        return DB::transaction(function () use ($batch, $data) {
            $recipients = $data['recipients'] ?? [];
            if (empty($recipients)) {
                abort(422, 'Minimal harus ada 1 karyawan penerima untuk multi-surat.');
            }

            $existing = $batch->documents()->get()->keyBy('id');
            $sub = 1;
            $newDocIds = [];

            foreach ($recipients as $r) {
                $employeeId = (int) ($r['employee_id'] ?? 0);
                if ($employeeId <= 0) {
                    continue;
                }

                $signees = $this->resolveBatchSignees($data, $employeeId);

                // Cari dokumen batch dengan employee yang sama
                $doc = $batch->documents()->where('employee_id', $employeeId)->first();

                if ($doc) {
                    $doc->update([
                        'employee1_signee_id' => $signees[1],
                        'employee2_signee_id' => $signees[2],
                        'employee3_signee_id' => $signees[3],
                        'content'             => $r['content'] ?? $doc->content,
                        'title'               => $r['title'] ?? $doc->title,
                        'document_name'       => $r['title'] ?? $doc->document_name,
                        'nomor_sub'           => $sub,
                        'tanggal_mulai'       => $this->normalizeDate($data['tanggal_mulai'] ?? null),
                        'tanggal_akhir'       => $this->normalizeDate($data['tanggal_akhir'] ?? null),
                    ]);
                    $newDocIds[] = $doc->id;
                } else {
                    $doc = ESign::create([
                        'employee_id'         => $employeeId,
                        'employee1_signee_id' => $signees[1],
                        'employee2_signee_id' => $signees[2],
                        'employee3_signee_id' => $signees[3],
                        'letter_type_id'      => $data['letter_type_id'] ?? $batch->letter_type_id,
                        'template_id'         => $data['template_id'] ?? $batch->template_id,
                        'batch_id'            => $batch->id,
                        'nomor_sub'           => $sub,
                        'nomor_surat'         => null,
                        'document_name'       => $r['title'] ?? $data['title'] ?? 'Draft Surat',
                        'title'               => $r['title'] ?? $data['title'] ?? null,
                        'content'             => $r['content'] ?? $data['content'] ?? '',
                        'document_type'       => 'contract',
                        'jenis_surat_slug'    => $data['jenis_surat_slug'] ?? $batch->jenis_surat_slug,
                        'description'         => $data['description'] ?? $batch->description ?? null,
                        'document_path'       => '',
                        'file_name'           => '',
                        'file_size'           => 0,
                        'status'              => ESign::STATUS_DRAFT,
                        'created_by'          => Auth::id(),
                        'upload_date'         => Carbon::now(),
                        'tanggal_mulai'       => $this->normalizeDate($data['tanggal_mulai'] ?? null),
                        'tanggal_akhir'       => $this->normalizeDate($data['tanggal_akhir'] ?? null),
                    ]);
                    $newDocIds[] = $doc->id;
                }
                $sub++;
            }

            // Hapus dokumen batch yang tidak ada lagi di recipients.
            // Hanya dokumen berstatus Draft yang boleh dihapus; dokumen yang
            // sudah diproses (sign/completed) tidak boleh ikut terhapus.
            $batch->documents()
                ->whereNotIn('id', $newDocIds)
                ->where('status', ESign::STATUS_DRAFT)
                ->delete();

            $batch->update(['total_recipients' => count($newDocIds)]);

            return $batch->fresh('documents');
        });
    }

    /**
     * Kirim batch multi-surat: generate nomor batch & status sign_1 untuk
     * semua dokumen, lalu kirim notifikasi serentak ke HR (sign 1) dan ke
     * semua karyawan penerima (sign 2).
     *
     * @param \App\Models\ESignBatch $batch
     * @return \App\Models\ESignBatch
     */
    public function sendBatch(ESignBatch $batch): ESignBatch
    {
        if ($batch->nomor_surat) {
            abort(403, 'Batch ini sudah dikirim.');
        }

        return DB::transaction(function () use ($batch) {
            $docs = $batch->documents()->get();
            if ($docs->isEmpty()) {
                abort(422, 'Batch tidak memiliki dokumen untuk dikirim.');
            }

            // Generate nomor batch: {PREFIX}/{DEPT}/{BULAN}/{TAHUN}
            $batchNumber = $this->numberService->generateNextBatchNumber($batch->jenis_surat_slug);

            $batch->update(['nomor_surat' => $batchNumber]);

            foreach ($docs as $doc) {
                $doc->update([
                    'nomor_surat' => $batchNumber . '/' . str_pad((string) $doc->nomor_sub, 3, '0', STR_PAD_LEFT),
                    'status'      => ESign::STATUS_SIGN_1,
                ]);
                $doc = $doc->fresh();

                // Notifikasi serentak: HR (sign 1) dan karyawan penerima (sign 2)
                $this->sendSignNotification($doc, 1);
                $this->sendSignNotification($doc, 2);
            }

            return $batch->fresh('documents');
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
        // ============================================================
        // MULTI-SURAT (batch): tanda tangan paralel.
        // HR (sign 1) & karyawan (sign 2) bisa tanda tangan kapan saja,
        // tidak saling menunggu. Selesai saat keduanya sudah menandatangani.
        // ============================================================
        if ($eSign->batch_id) {
            if ($eSign->employee1_signee_id == $employeeId && $eSign->employee1_signed_at) {
                abort(403, 'Anda sudah menandatangani surat ini.');
            }
            if ($eSign->employee2_signee_id == $employeeId && $eSign->employee2_signed_at) {
                abort(403, 'Anda sudah menandatangani surat ini.');
            }
            if ($eSign->employee1_signee_id != $employeeId && $eSign->employee2_signee_id != $employeeId) {
                abort(403, 'Anda tidak terdaftar sebagai penandatangan surat ini.');
            }

            return DB::transaction(function () use ($eSign, $employeeId) {
                $now = now();
                $updateData = [];

                if ($eSign->employee1_signee_id == $employeeId) {
                    $updateData['employee1_signed_at'] = $now;
                } else {
                    $updateData['employee2_signed_at'] = $now;
                }

                $current = $eSign->fresh();
                $s1 = $updateData['employee1_signed_at'] ?? $current->employee1_signed_at;
                $s2 = $updateData['employee2_signed_at'] ?? $current->employee2_signed_at;

                // Label status akurat: selesai hanya bila keduanya menandatangani,
                // jika belum lengkap tampilkan pihak yang masih menunggu.
                $updateData['status'] = ($s1 && $s2)
                    ? ESign::STATUS_COMPLETED
                    : ($s1 ? ESign::STATUS_SIGN_2 : ESign::STATUS_SIGN_1);

                $eSign->update($updateData);
                $eSign = $eSign->fresh();

                // Ingatkan pihak yang masih belum menandatangani.
                if ($updateData['status'] !== ESign::STATUS_COMPLETED) {
                    $this->sendSignNotification($eSign, $s1 ? 2 : 1);
                }

                return $eSign;
            });
        }

        // ============================================================
        // Surat tunggal TANDA TANGAN PARALEL (kirim via "Langsung Kirim").
        // Sign 1 & Sign 2 bisa tanda tangan kapan saja; selesai saat keduanya sdh tandatangan.
        // ============================================================
        if ($eSign->is_parallel_sign) {
            if ($eSign->employee1_signee_id == $employeeId && $eSign->employee1_signed_at) {
                abort(403, 'Anda sudah menandatangani surat ini.');
            }
            if ($eSign->employee2_signee_id == $employeeId && $eSign->employee2_signed_at) {
                abort(403, 'Anda sudah menandatangani surat ini.');
            }
            if ($eSign->employee1_signee_id != $employeeId && $eSign->employee2_signee_id != $employeeId) {
                abort(403, 'Anda tidak terdaftar sebagai penandatangan surat ini.');
            }

            return DB::transaction(function () use ($eSign, $employeeId) {
                $now = now();
                $updateData = [];

                if ($eSign->employee1_signee_id == $employeeId) {
                    $updateData['employee1_signed_at'] = $now;
                } else {
                    $updateData['employee2_signed_at'] = $now;
                }

                $current = $eSign->fresh();
                $s1 = $updateData['employee1_signed_at'] ?? $current->employee1_signed_at;
                $s2 = $updateData['employee2_signed_at'] ?? $current->employee2_signed_at;

                // Label status akurat: selesai hanya bila keduanya menandatangani,
                // jika belum lengkap tampilkan pihak yang masih menunggu.
                $updateData['status'] = ($s1 && $s2)
                    ? ESign::STATUS_COMPLETED
                    : ($s1 ? ESign::STATUS_SIGN_2 : ESign::STATUS_SIGN_1);

                $eSign->update($updateData);
                $eSign = $eSign->fresh();

                // Ingatkan pihak yang masih belum menandatangani.
                if ($updateData['status'] !== ESign::STATUS_COMPLETED) {
                    $this->sendSignNotification($eSign, $s1 ? 2 : 1);
                }

                return $eSign;
            });
        }

        // Surat tunggal: hanya giliran yang bisa menandatangani
        if (!$eSign->canBeResponded($employeeId)) {
            abort(403, 'Anda tidak berhak menandatangani surat ini saat ini.');
        }

        return DB::transaction(function () use ($eSign, $employeeId) {
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
     * Kirim beberapa draft surat sekaligus ke employee masing-masing.
     *
     * Setiap surat draft yang dipilih dikirim per surat (individu) melalui
     * sendToEmployee(): generate nomor surat, status -> sign_1, email ke Sign 1.
     * Hanya surat dengan status draft yang benar-benar dikirim.
     *
     * @param  int[]  $ids
     * @return int  Jumlah surat yang berhasil dikirim.
     */
    public function sendSelectedToEmployees(array $ids): int
    {
        $esigns = ESign::query()
            ->whereIn('id', $ids)
            ->get()
            ->filter(fn (ESign $e) => $e->canBeSent());

        $count = 0;
        foreach ($esigns as $eSign) {
            $this->sendToEmployee($eSign);
            $count++;
        }

        return $count;
    }

    /**
     * Kirim draft surat tunggal sekaligus ke Sign 1 & Sign 2.
     *
     * Dipakai tombol "Langsung Kirim ke Employee" pada editor surat.
     * Men-generate nomor surat, set status sign_1, lalu mengirim email ke
     * penandatangan pertama (Sign 1) dan kedua (Sign 2) sekaligus, sehingga
     * keduanya melihat surat pada menu "sign employe" masing-masing.
     */
    public function sendSingleToBoth(ESign $eSign): ESign
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
                'is_parallel_sign' => true,
            ]);

            $eSign = $eSign->fresh();

            // Kirim email ke Sign 1 & Sign 2
            $this->sendSignNotification($eSign, 1);
            $this->sendSignNotification($eSign, 2);

            return $eSign;
        });
    }

    /**
     * Employee rejects a document.
     * Hanya penandatangan yang berhak menolak (identitas dari user yang login),
     * konsisten dengan logika otorisasi pada approveByEmployee().
     */
    public function rejectByEmployee(ESign $eSign, int $employeeId): ESign
    {
        // Batch (multi-surat) / paralel: penolak harus Sign 1 atau Sign 2.
        if ($eSign->batch_id || $eSign->is_parallel_sign) {
            $isSignee = in_array($employeeId, [
                $eSign->employee1_signee_id,
                $eSign->employee2_signee_id,
            ], true);

            if (!$isSignee) {
                abort(403, 'Anda tidak terdaftar sebagai penandatangan surat ini.');
            }
        }
        // Surat tunggal berurutan: hanya giliran yang boleh menolak.
        elseif (!$eSign->canBeResponded($employeeId)) {
            abort(403, 'Anda tidak berhak menolak surat ini saat ini.');
        }

        return DB::transaction(function () use ($eSign) {
            $eSign->update(['status' => ESign::STATUS_REJECTED_EMPLOYEE]);

            return $eSign->fresh();
        });
    }

    /**
     * Normalize an incoming date string into a DB-safe 'Y-m-d' format.
     *
     * Datepicker & field tanggal dikirim dalam format id-ID (mis. "03 Agustus 2026"
     * atau "3 Agustus 2026"), sementara kolom di DB bertipe date. Konversi ini
     * mencegah error "Could not parse ... Failed to parse time string".
     *
     * Format yang dikenali:
     * - "2026-08-03"           (sudah format DB)
     * - "03/08/2026" / "3/8/2026"
     * - "03 Agustus 2026"      (id-ID, dengan/tanpa leading zero)
     *
     * @param mixed $value
     * @return string|null
     */
    private function normalizeDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim((string) $value);

        // Sudah dalam format Y-m-d
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        // Format n/j/Y atau dd/mm/YYYY
        if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $value, $m)) {
            return sprintf('%04d-%02d-%02d', (int) $m[3], (int) $m[2], (int) $m[1]);
        }

        // Format id-ID: "03 Agustus 2026"
        $indonesianMonths = [
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
            'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
            'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12,
        ];
        if (preg_match('#^(\d{1,2})\s+([a-zA-Z]+)\s+(\d{4})$#', $value, $m)) {
            $month = strtolower($m[2]);
            if (isset($indonesianMonths[$month])) {
                return sprintf('%04d-%02d-%02d', (int) $m[3], $indonesianMonths[$month], (int) $m[1]);
            }
            // Bulan Inggris fallback
            try {
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }

        // Fallback: coba parse otomatis
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
