<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTicket extends Model
{
    use HasFactory;

    // protected $fillable = [
    //     'no_ticket',
    //     'subject',
    //     'description',
    //     'report_for',
    //     'submitter_id',
    //     'pic_id',
    //     'catalog',
    //     'type',
    //     'priority',
    //     'visibility',
    //     'it_asset_id',
    //     'current_status',
    //     'time_release'
    // ];

    protected $guarded = ['id'];

    protected $casts = [
        'supervisor_approval_at' => 'datetime',
        'dept_head_approval_at' => 'datetime',
        'submitted_for_approval_at' => 'datetime',
    ];

    protected $appends = ['total_score'];

    public const REPORT_SELF = 'self';
    public const REPORT_OTHER = 'other';

    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_INTERNAL = 'internal';

    public const TYPE_INCIDENT = 'incident';
    public const TYPE_REQUEST = 'request';
    public const TYPE_CHANGE = 'change';
    public const TYPE_IT_INITIATIVE = 'it_initiative';

    public const APPROVAL_STATUS_PENDING = 'pending';
    public const APPROVAL_STATUS_APPROVED = 'approved';
    public const APPROVAL_STATUS_REJECTED = 'rejected';

    public const ROLE_USER =  'user';
    public const ROLE_IT = 'it';
    public const ROLE_SYSTEM = 'system';
    public const ROLE_SUPERVISOR = 'supervisor';
    public const ROLE_DEPT_HEAD = 'dept_head';
    public const ROLE_SERVICE_CHANGE = 'service_change';
    public const ROLE_CC = 'cc';
    public const ROLE_VIEWER = 'viewer'; // public viewer, no action allowed
    
    public function getTotalScoreAttribute() {
        $impact = $this->impact ?? 0;
        $urgency = $this->urgency ?? 0;
        $scope = $this->scope ?? 0;
        $riskScore = $this->risk_register_score ?? 0;

        return ($impact * $urgency + $scope + $riskScore) == 0 ? 99999999 : ($impact * $urgency + $scope + $riskScore);
    }
    
    public function media()
    {
        return parent::hasMany(ServiceTicketMedia::class);
    }

    public function histories()
    {
        return parent::hasMany(ServiceStatusHistory::class);
    }

    public function latestHistory() {
        return $this->histories->sortByDesc('id')->first();
    }

    public function messages()
    {
        return parent::hasMany(ServiceTicketMessage::class);
    }

    public function submitter()
    {
        return parent::belongsTo(Employee::class, 'submitter_id', 'id');
    }

    public function ccs()
    {
        return parent::hasMany(ServiceTicketCC::class)->with('employee');
    }

    public function itAssets()
    {
        return parent::belongsToMany(ITAsset::class, ServiceTicketAsset::class, 'service_ticket_id', 'it_asset_id')->withPivot('employee_id');
    }

    public function employeeCcs()
    {
        return parent::belongsToMany(Employee::class, ServiceTicketCC::class, 'service_ticket_id', 'employee_id', 'id', 'id');
    }

    public function serviceChange() {
        return parent::hasOne(ServiceChange::class, 'service_ticket_id', 'id');
    }

    public function supervisor() {
        return $this->belongsTo(Employee::class, 'supervisor_id');
    }

    public function deptHead() {
        return $this->belongsTo(Employee::class, 'dept_head_id');
    }

    public function priority() {
        return $this->belongsTo(ItsmPriority::class, 'itsm_priority_id', 'id');
    }

    public function riskRegister() {
        return $this->belongsTo(RiskRegister::class, 'risk_register_id', 'id');
    }

    public function reportFor() {
        return $this->belongsTo(Employee::class, 'report_for_id', 'id');
    }

    public function itHandler() {
        return $this->belongsTo(Employee::class, 'it_handler_id', 'id');
    }
}
