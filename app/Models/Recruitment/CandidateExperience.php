<?php

namespace App\Models\Recruitment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateExperience extends Model
{
    use HasFactory;
    protected $table = 'candidate_experience';
    protected $fillable = [
        'candidate_id',
        'company',
        'position',
        'years',
    ];
    protected $casts = [
        'years' => 'integer',
    ];
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }
}
