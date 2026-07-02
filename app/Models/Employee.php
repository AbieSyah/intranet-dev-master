<?php

namespace App\Models;

use App\Models\AssetDisposal;
use App\Models\Master\Appraisal;
use App\Models\Master\Building;
use App\Models\Master\LineApproval;
use App\Models\Master\LineApprovalEmployee;
use App\Models\User;
use App\Models\EmployeeMilestone;
use App\Models\Section;
use App\Models\Position;
use App\Models\Level;
use App\Models\Master\Contract;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attendance\EmployeeAttendance;
use App\Models\Attendance\GroupEmployee;
use App\Models\Attendance\LeaveBalance;

class Employee extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nik',
        'no_ktp',
        'fullname',
        'email',
        'addressktp',
        'birthplace',
        'birthdate',
        'religion',
        'joindate',
        'enddate',
        'marital',
        'gender',
        'hp',
        'department_id',
        'area_id',
        'section_id',
        'position_id',
        'level_id',
        'work_location',
        'status',
        'reason',
        'avatar',

        'building_id',
        'contract_startdate',
        'contract_number',

        'domicile_address',
        'emergency_contact',
        'emergency_contact_relation',
        'emergency_contact_handphone',
        'emergency_contact_address',
        'permanent_startdate',
        'iso_position',
        'cost_center',
        'last_education',
        'major_last_education',
        'last_education_institutional',
        'tax_dependents',
        'npwp',
        
        'outsourcing_vendor',
        'bpjs_kesehatan',
        'bpjs_ketenagakerjaan',
        'latest_agreement_number',
        'active_agreement_number',
        'bank_name',
        'bank_account',
        'bank_account_holder',
        'blood_type',
    ];

    public const LAST_EDUCATIONS = [
        'Elementary School' => 'SD',
        'Junior High School' => 'SMP',
        'Senior High School' => 'SMA/SMK',
        'Diploma 1' => 'D1',
        'Diploma 2' => 'D2',
        'Diploma 3' => 'D3',
        "Bachelor's Degree" => 'D4/S1',
        "Master's Degree" => 'S2',
        'Doctoral Degree' => 'S3',
        'Pharmacist Profession Degree' => 'Apoteker',
    ];

    public const BLOOD_TYPES = ['A', 'B', 'AB', 'O'];

    public const RELIGIONS = [
        'Islam',
        'Catholic',
        'Christian',
        'Buddha',
        'Hindu',
        'Others'
    ];

    public const MARITAL_STATUSES = [
        'Single'   => [
            'label' => 'Single', 
            'title' => ''
        ],
        'Married'  => [
            'label' => 'Married', 
            'title' => ''
        ],
        'Divorced' => [
            'label' => 'Divorced', 
            'title' => ''
        ],
        'Widow'    => [
            'label' => 'Widow (Female)', 
            'title' => 'A woman whose her husband has died and has not married again'
        ],
        'Widower'  => [
            'label' => 'Widower (Male)', 
            'title' => 'A man whose his wife has died and has not married again'
        ]
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'employee_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function appraisals()
    {
        return $this->hasMany(Appraisal::class);
    }

    public function LineApprovals()
    {
        return $this->hasManyThrough(
            LineApproval::class, 
            LineApprovalEmployee::class,
            'employee_id',
            'id',
            'id',
            'line_approval_id'
        );
    }

    public function assetDisposals()
    {
        return $this->hasMany(AssetDisposal::class, 'requester_id');
    }

    public function milestones()
    {
        return $this->hasMany(EmployeeMilestone::class);
    }

    public static function getTaxDependentsOptions()
    {
        return [
            'TK/0',
            'TK/1',
            'TK/2',
            'TK/3',
            'K/0',
            'K/1',
            'K/2',
            'K/3',
        ];
    }

    protected function age(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->birthdate) return '-';
                $birthDate = Carbon::parse($this->birthdate);
                $now = Carbon::now();
                $diff = $birthDate->diff($now);
                return $diff->format('%y Years %m Months');
            }
        );
    }
    
    protected function serviceYears(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->joindate) return '-';
                $joinDate = Carbon::parse($this->joindate);
                $endDate = ($this->status === 'TERMINATED' && !empty($this->enddate)) 
                            ? Carbon::parse($this->enddate) 
                            : Carbon::now();
                if ($endDate->lt($joinDate)) return '0 Years 0 Months';
                $diff = $joinDate->diff($endDate);
                return $diff->format('%y Years %m Months');
            }
        );
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_number');
    }
    public function groupEmployees(){
        return $this->hasOne(GroupEmployee::class, 'employee_id');
    }
    public function leaveBalance()
    {
        return $this->hasMany(LeaveBalance::class, 'employee_id');
    }
    public function attendances()
    {
        return $this->hasMany(EmployeeAttendance::class, 'employee_id');
    }
}