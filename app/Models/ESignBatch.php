<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ESignBatch extends Model
{
    use HasFactory;

    protected $table = 'e_sign_batches';

    protected $fillable = [
        'nomor_surat',
        'jenis_surat_slug',
        'letter_type_id',
        'template_id',
        'created_by',
        'total_recipients',
    ];

    // ========== RELATIONSHIPS ==========

    public function documents()
    {
        return $this->hasMany(ESign::class, 'batch_id');
    }

    public function letterType()
    {
        return $this->belongsTo(LetterType::class, 'letter_type_id');
    }

    public function template()
    {
        return $this->belongsTo(ESignTemplate::class, 'template_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
