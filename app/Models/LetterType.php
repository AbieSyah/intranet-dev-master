<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LetterType extends Model
{
    protected $table = 'letter_types';

    protected $fillable = [
        'slug',
        'name',
        'prefix',
        'description',
        'icon',
        'color',
        'is_active',
        'multi_enabled',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'multi_enabled' => 'boolean',
    ];

    // ========== RELATIONSHIPS ==========

    public function templates()
    {
        return $this->hasMany(ESignTemplate::class, 'letter_type_id');
    }

    public function activeTemplate()
    {
        return $this->hasOne(ESignTemplate::class, 'letter_type_id')
            ->where('is_active', true);
    }

    public function documents()
    {
        return $this->hasMany(ESign::class, 'jenis_surat_slug', 'slug');
    }

    // ========== ACCESSORS ==========

    public function getTemplateCountAttribute()
    {
        return $this->templates()->count();
    }

    public function getDocumentCountAttribute()
    {
        return $this->documents()->count();
    }

    public function getStatusBadgeAttribute()
    {
        return $this->is_active
            ? '<span class="badge bg-success">Aktif</span>'
            : '<span class="badge bg-secondary">Nonaktif</span>';
    }

    public function getHasActiveTemplateAttribute()
    {
        return $this->activeTemplate()->exists();
    }

    public function getTemplateStatusLabelAttribute()
    {
        if ($this->has_active_template) {
            return '<span class="text-success fw-semibold"><i class="ri-checkbox-circle-fill me-1"></i> Template tersedia</span>';
        }
        if ($this->template_count > 0) {
            return '<span class="text-warning fw-semibold"><i class="ri-error-warning-fill me-1"></i> Template nonaktif</span>';
        }
        return '<span class="text-secondary"><i class="ri-close-circle-fill me-1"></i> Belum ada template</span>';
    }

    // ========== SCOPES ==========

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
