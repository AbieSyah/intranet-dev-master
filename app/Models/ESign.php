<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESign extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'e_signs';

    protected $fillable = [
        'employee_id',
        'employee1_signee_id',
        'employee2_signee_id',
        'employee3_signee_id',
        'employee1_signed_at',
        'employee2_signed_at',
        'employee3_signed_at',
        'recipient_acknowledged_at',
        'letter_type_id',
        'template_id',
        'batch_id',
        'nomor_sub',
        'is_parallel_sign',
        'nomor_surat',
        'document_name',
        'title',
        'content',
        'document_type',
        'jenis_surat_slug',
        'description',
        'document_path',
        'file_name',
        'file_size',
        'pdf_path',
        'signed_pdf_path',
        'status',
        'created_by',
        'upload_date',
        'tanggal_mulai',
        'tanggal_akhir',
    ];

    protected $dates = [
        'upload_date',
        'tanggal_mulai',
        'tanggal_akhir',
        'recipient_acknowledged_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'upload_date' => 'datetime',
        'tanggal_mulai' => 'date',
        'tanggal_akhir' => 'date',
        'recipient_acknowledged_at' => 'datetime',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Get the employee associated with this ESign document
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the letter type associated with this document.
     */
    public function letterType()
    {
        return $this->belongsTo(LetterType::class, 'letter_type_id');
    }

    /**
     * Get the template used when this draft was created.
     */
    public function template()
    {
        return $this->belongsTo(ESignTemplate::class, 'template_id');
    }

    /**
     * Batch induk untuk dokumen multi-surat (nullable untuk surat tunggal).
     */
    public function batch()
    {
        return $this->belongsTo(ESignBatch::class, 'batch_id');
    }

    /**
     * Get the user who created this draft.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ========== CONSTANTS ==========

    const STATUS_DRAFT = 'draft';
    const STATUS_SIGN_1 = 'sign_1';
    const STATUS_SIGN_2 = 'sign_2';
    const STATUS_SIGN_3 = 'sign_3';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED_EMPLOYEE = 'rejected_employee';
    const STATUS_AWAITING_ACK = 'awaiting_ack';
    const STATUS_ACKNOWLEDGED = 'acknowledged';

    const DOCUMENT_TYPE_CONTRACT = 'contract';
    const DOCUMENT_TYPE_APPROVAL = 'approval';
    const DOCUMENT_TYPE_AGREEMENT = 'agreement';
    const DOCUMENT_TYPE_OTHER = 'other';

    // ========== STATUS MAPPING ==========

    public static $statuses = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_SIGN_1 => 'Menunggu Sign 1',
        self::STATUS_SIGN_2 => 'Menunggu Sign 2',
        self::STATUS_SIGN_3 => 'Menunggu Sign 3',
        self::STATUS_AWAITING_ACK => 'Menunggu Konfirmasi',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_ACKNOWLEDGED => 'Dikonfirmasi',
        self::STATUS_REJECTED_EMPLOYEE => 'Ditolak',
    ];

    public static $documentTypes = [
        self::DOCUMENT_TYPE_CONTRACT => 'Kontrak',
        self::DOCUMENT_TYPE_APPROVAL => 'Persetujuan',
        self::DOCUMENT_TYPE_AGREEMENT => 'Perjanjian',
        self::DOCUMENT_TYPE_OTHER => 'Lainnya',
    ];

    // ========== ACCESSORS & MUTATORS ==========

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return self::$statuses[$this->status] ?? 'Unknown';
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            self::STATUS_DRAFT => 'warning',
            self::STATUS_SIGN_1 => 'info',
            self::STATUS_SIGN_2 => 'info',
            self::STATUS_SIGN_3 => 'info',
            self::STATUS_AWAITING_ACK => 'warning',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_ACKNOWLEDGED => 'success',
            self::STATUS_REJECTED_EMPLOYEE => 'danger',
        ];

        $color = $colors[$this->status] ?? 'secondary';
        $label = $this->status_label;

        return "<span class='badge bg-{$color}'>{$label}</span>";
    }

    /**
     * Get document type label
     */
    public function getDocumentTypeLabelAttribute()
    {
        return self::$documentTypes[$this->document_type] ?? 'Unknown';
    }

    /**
     * Get formatted file size
     */
    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $i < count($units) && $bytes >= 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get jenis surat label (display name from slug).
     * Map ini harus sinkron dengan ESignDummyData::getLetterTypes()
     */
    public static function getJenisSuratLabels(): array
    {
        return [
            'pkwt' => 'PKWT',
            'promosi' => 'Promosi',
            'mutasi' => 'Mutasi',
            'demosi' => 'Demosi',
            'perpanjangan-pkwt' => 'Perpanjangan PKWT',
            'pengangkatan' => 'Pengangkatan Karyawan Tetap',
            'surat-peringatan' => 'Surat Peringatan',
        ];
    }

    /**
     * Get jenis surat display name from slug
     */
    public function getJenisSuratLabelAttribute()
    {
        $labels = self::getJenisSuratLabels();
        return $labels[$this->jenis_surat_slug] ?? ucfirst($this->jenis_surat_slug);
    }

    /**
     * Get formatted tanggal_mulai (e.g. "01 Jul 2026")
     */
    public function getTanggalMulaiFormattedAttribute()
    {
        if (!$this->tanggal_mulai) return '-';
        return Carbon::parse($this->tanggal_mulai)->format('d M Y');
    }

    /**
     * Get formatted tanggal_akhir (e.g. "01 Jul 2026")
     */
    public function getTanggalAkhirFormattedAttribute()
    {
        if (!$this->tanggal_akhir) return '-';
        return Carbon::parse($this->tanggal_akhir)->format('d M Y');
    }

    /**
     * Get prefix nomor surat berdasarkan jenis_surat_slug.
     * Sinkron dengan prefix di ESignDummyData.
     */
    public static function getNomorSuratPrefixes(): array
    {
        return [
            'pkwt' => 'PKWT',
            'promosi' => 'PROM',
            'mutasi' => 'MUT',
            'demosi' => 'DEM',
            'perpanjangan-pkwt' => 'PPKWT',
            'pengangkatan' => 'ANGKAT',
            'surat-peringatan' => 'SP',
        ];
    }

    /**
     * Get the prefix for this document's letter type
     */
    public function getNomorSuratPrefixAttribute()
    {
        $prefixes = self::getNomorSuratPrefixes();
        return $prefixes[$this->jenis_surat_slug] ?? strtoupper($this->jenis_surat_slug);
    }

    // ========== QUERY SCOPES ==========

    /**
     * Scope to get documents by employee
     */
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope to get draft documents
     */
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Scope to get documents waiting for sign (sign_1, sign_2, sign_3)
     */
    public function scopeWaitingSign($query)
    {
        return $query->whereIn('status', [self::STATUS_SIGN_1, self::STATUS_SIGN_2, self::STATUS_SIGN_3]);
    }

    /**
     * Scope to get completed documents
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope to get rejected documents
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED_EMPLOYEE);
    }

    /**
     * Scope to get by document type
     */
    public function scopeByType($query, $documentType)
    {
        return $query->where('document_type', $documentType);
    }

    /**
     * Scope to get by jenis surat slug
     */
    public function scopeByJenisSurat($query, $slug)
    {
        return $query->where('jenis_surat_slug', $slug);
    }

    // ========== HELPER METHODS ==========

    /**
     * Check if document is in draft status
     */
    public function isDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if document is in sign_1 status
     */
    public function isSign1()
    {
        return $this->status === self::STATUS_SIGN_1;
    }

    /**
     * Check if document is in sign_2 status
     */
    public function isSign2()
    {
        return $this->status === self::STATUS_SIGN_2;
    }

    /**
     * Check if document is in sign_3 status
     */
    public function isSign3()
    {
        return $this->status === self::STATUS_SIGN_3;
    }

    /**
     * Check if document is completed
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if document is rejected by employee
     */
    public function isRejectedByEmployee()
    {
        return $this->status === self::STATUS_REJECTED_EMPLOYEE;
    }

    /**
     * Get current sign level (1, 2, 3) based on status.
     * Returns 0 if not in sign_1/2/3.
     */
    public function getCurrentSignLevel(): int
    {
        return match($this->status) {
            self::STATUS_SIGN_1 => 1,
            self::STATUS_SIGN_2 => 2,
            self::STATUS_SIGN_3 => 3,
            default => 0,
        };
    }

    /**
     * Get employee ID who should sign at the current level.
     */
    public function getCurrentSigneeId(): ?int
    {
        return match($this->status) {
            self::STATUS_SIGN_1 => $this->employee1_signee_id,
            self::STATUS_SIGN_2 => $this->employee2_signee_id,
            self::STATUS_SIGN_3 => $this->employee3_signee_id,
            default => null,
        };
    }

    /**
     * Check if document can be responded to by the recipient employee.
     * Only the employee whose turn it is (current sign level) can respond.
     */
    public function canBeResponded(int $employeeId): bool
    {
        $currentSigneeId = $this->getCurrentSigneeId();
        return $currentSigneeId !== null && $currentSigneeId === $employeeId;
    }

    /**
     * Check if this document is a "recipient-only" letter: has a recipient
     * (employee_id) that is NOT one of the signers. Such letters go through
     * the acknowledgment flow instead of being signed by the recipient.
     */
    public function hasRecipientOnly(): bool
    {
        if (!$this->employee_id) {
            return false;
        }
        return !in_array($this->employee_id, [
            $this->employee1_signee_id,
            $this->employee2_signee_id,
            $this->employee3_signee_id,
        ], true);
    }

    /**
     * Check if the document is waiting for the recipient to confirm reading.
     */
    public function isAwaitingAck(): bool
    {
        return $this->status === self::STATUS_AWAITING_ACK;
    }

    /**
     * Check if the recipient has acknowledged the document.
     */
    public function isAcknowledged(): bool
    {
        return $this->status === self::STATUS_ACKNOWLEDGED;
    }

    /**
     * Check if the given employee may acknowledge (confirm reading) this document.
     */
    public function canAcknowledge(int $employeeId): bool
    {
        return $this->isAwaitingAck()
            && $this->employee_id !== null
            && $this->employee_id === $employeeId;
    }

    /**
     * Check if document is waiting for any employee sign
     */
    public function isWaitingSign()
    {
        return in_array($this->status, [self::STATUS_SIGN_1, self::STATUS_SIGN_2, self::STATUS_SIGN_3]);
    }

    /**
     * Check if document can be sent to employee (only draft)
     */
    public function canBeSent()
    {
        return $this->isDraft();
    }

    /**
     * Get the status badge color for views that don't use the accessor
     */
    public static function getStatusColor(string $status): string
    {
        $colors = [
            self::STATUS_DRAFT => 'warning',
            self::STATUS_SIGN_1 => 'info',
            self::STATUS_SIGN_2 => 'info',
            self::STATUS_SIGN_3 => 'info',
            self::STATUS_AWAITING_ACK => 'warning',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_ACKNOWLEDGED => 'success',
            self::STATUS_REJECTED_EMPLOYEE => 'danger',
        ];
        return $colors[$status] ?? 'secondary';
    }

    /**
     * Check if document can be edited
     */
    public function canEdit()
    {
        return $this->isDraft();
    }

    /**
     * Check if document can be deleted
     */
    public function canDelete()
    {
        return $this->isDraft();
    }
}
