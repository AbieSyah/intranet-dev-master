<?php

namespace App\Models\Recruitment;

use App\Models\Area;
use App\Models\Department;
use App\Models\Position;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Candidate extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'candidate';
    protected $fillable = [
        'posting_id',
        'position_id',
        'department_id',
        'section_id',
        'area_id',

        'no_ktp',
        'fullname',
        'nickname',
        'ktp_address',
        'domicile_address',
        'phone',
        'email',
        'birthplace',
        'birthdate',
        'gender',
        'religion',
        'marital',
        'height',
        'weight',
        'skill',
        'expected_salary',
        'submit_date',
        'photo',

        'ip_address',
        'user_agent',
        'referer_source',
        'captcha_verified_at',
    ];
    protected $casts = [
        'birthdate' => 'date',
        'submit_date' => 'datetime',
        'height' => 'integer',
        'weight' => 'integer',
        'captcha_verified_at' => 'datetime',
    ];
    public function posting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class, 'posting_id');
    }
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }
    public function routeNotificationForMail(): string
    {
        return $this->email;
    }
    public function experiences(): HasMany
    {
        return $this->hasMany(CandidateExperience::class, 'candidate_id');
    }
    public function educations(): HasMany
    {
        return $this->hasMany(CandidateEducation::class, 'candidate_id'); 
    }
    public function selections(): BelongsToMany
    {
        return $this->belongsToMany(
            SelectionProcess::class,
            'selection_process_candidates',
            'candidate_id',
            'selection_process_id'
        )
        ->withPivot('result_status', 'comment', 'email_notification_sent_at');
    }
}
