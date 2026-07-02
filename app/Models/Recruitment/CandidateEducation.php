<?php

namespace App\Models\Recruitment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateEducation extends Model
{
    use HasFactory;
    protected $table = 'candidate_education';
    protected $fillable = [
        'candidate_id',
        'level',
        'institution_name',
        'major',
        'year_graduated',
        'score_gpa',
        'ijazah',
    ];
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }
}
