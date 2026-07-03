<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ESign extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'e_signs';

    protected $fillable = [
        'employee_id',
        'document_name',
        'document_type',
        'description',
        'document_path',
        'file_name',
        'file_size',
        'status',
        'upload_date',
    ];

    protected $dates = [
        'upload_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'upload_date' => 'datetime',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Get the employee associated with this ESign document
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // ========== CONSTANTS ==========

    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    const DOCUMENT_TYPE_CONTRACT = 'contract';
    const DOCUMENT_TYPE_APPROVAL = 'approval';
    const DOCUMENT_TYPE_AGREEMENT = 'agreement';
    const DOCUMENT_TYPE_OTHER = 'other';

    // ========== STATUS MAPPING ==========

    public static $statuses = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_PENDING => 'Menunggu Persetujuan',
        self::STATUS_APPROVED => 'Disetujui',
        self::STATUS_REJECTED => 'Ditolak',
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
            self::STATUS_PENDING => 'info',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
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
     * Scope to get pending documents
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to get approved documents
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope to get by document type
     */
    public function scopeByType($query, $documentType)
    {
        return $query->where('document_type', $documentType);
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
     * Check if document is in pending status
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if document is approved
     */
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if document is rejected
     */
    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
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
