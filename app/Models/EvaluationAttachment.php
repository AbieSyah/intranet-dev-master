<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EvaluationAttachment extends Model
{
    use HasFactory;
    protected $table = 'evaluation_attachments';

    protected $fillable = [
        'name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    public function evaluations(): BelongsToMany
    {
        return $this->belongsToMany(Evaluation::class, 'evaluation_has_attachments', 'attachment_id', 'evaluation_id');
    }
}
