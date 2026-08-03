<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ESignTemplate extends Model
{
    protected $table = 'esign_templates';

    protected $fillable = [
        'letter_type_id',
        'jenis_surat_slug',
        'title',
        'content',
        'template_type',
        'file_path',
        'file_original_name',
        'version',
        'is_active',
        'created_by',
        'updated_by',
        'page_margin_top',
        'page_margin_bottom',
        'page_margin_left',
        'page_margin_right',
        'page_size',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'version' => 'integer',
        'page_margin_top' => 'integer',
        'page_margin_bottom' => 'integer',
        'page_margin_left' => 'integer',
        'page_margin_right' => 'integer',
    ];

    // ========== RELATIONSHIPS ==========

    public function letterType()
    {
        return $this->belongsTo(LetterType::class, 'letter_type_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ========== ACCESSORS ==========

    public function getJenisSuratLabelAttribute()
    {
        $labels = ESign::getJenisSuratLabels();
        return $labels[$this->jenis_surat_slug] ?? ucfirst($this->jenis_surat_slug);
    }

    public function getUpdatedAtFormattedAttribute()
    {
        return $this->updated_at ? Carbon::parse($this->updated_at)->format('d M Y H:i') : '-';
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->is_active) {
            return '<span class="badge bg-success">Aktif</span>';
        }
        return '<span class="badge bg-secondary">Nonaktif</span>';
    }

    public function getTypeLabelAttribute()
    {
        $labels = [
            'editor' => 'HTML/Text',
            'docx' => 'DOCX',
            'pdf' => 'PDF',
            'html' => 'HTML',
        ];
        return $labels[$this->template_type] ?? strtoupper($this->template_type);
    }

    public function getTypeBadgeAttribute()
    {
        $colors = [
            'editor' => 'secondary',
            'docx' => 'primary',
            'pdf' => 'danger',
            'html' => 'info',
        ];
        $color = $colors[$this->template_type] ?? 'secondary';
        return "<span class=\"badge bg-{$color} bg-opacity-10 text-{$color}\">{$this->type_label}</span>";
    }

    public function getHasFileAttribute(): bool
    {
        return !empty($this->file_path) && in_array($this->template_type, ['docx', 'pdf']);
    }

    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }
        return asset('storage/' . $this->file_path);
    }

    // ========== SCOPES ==========

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByJenisSurat($query, $slug)
    {
        return $query->where('jenis_surat_slug', $slug);
    }

    public function scopeByLetterType($query, $letterTypeId)
    {
        return $query->where('letter_type_id', $letterTypeId);
    }
}
