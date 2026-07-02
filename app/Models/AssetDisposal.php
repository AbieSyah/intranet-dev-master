<?php

namespace App\Models;

use App\Models\AssetDisposalItem;
use App\Models\AssetDisposalLog;
use App\Models\DisposalApprovalPath;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AssetDisposal extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction_number',
        'requester_id',
        'reason',
        'buyer_name',
        'buyer_phone',
        'buyer_email',
        'buyer_address',
        'current_step',
        'doc_status',
        'current_status',
        'buyer_confirmed',
        'validated_at', // this date time is for buyer validation time
        'buyer_ip',
        'file_path'
    ];

    protected $casts = [
        'validated_at' => 'datetime'
    ];

    const STATUS_WAITING = 'waiting';
    const STATUS_APPROVED = 'approved';
    const STATUS_REVISION = 'revision';
    const STATUS_REJECTED = 'rejected';
    const STATUS_REVISED = 'revised';
    const STATUS_CANCELED = 'canceled';
    const STATUS_COMPLETE = 'complete';

    const DOC_STATUS_DRAFT = 'draft';
    const DOC_STATUS_APPROVED = 'approved';
    const DOC_STATUS_CANCELED = 'canceled';
    const DOC_STATUS_COMPLETE = 'complete';

    public function assetHistories() {
        return $this->morphMany(AssetHistory::class, 'reference');
    }

    public function approvalPaths()
    {
        return parent::hasMany(DisposalApprovalPath::class);
    }

    public function disposalItems()
    {
        return $this->hasMany(AssetDisposalItem::class);
    }

    public function requester() {
        return $this->belongsTo(Employee::class);
    }

    public function logs() {
        return $this->hasMany(AssetDisposalLog::class);
    }

    public function scopeCurrentStep(Builder $query) {
        return $this->approvalPaths->firstWhere('step_order', $this->current_step);
    }

    public function scopeMyPendingApproval(Builder $query) {
        $employeeId = Auth::user()->employee->id;

        return $query->where('current_status', AssetDisposal::STATUS_WAITING)->where('doc_status', AssetDisposal::DOC_STATUS_DRAFT)
            ->whereHas('approvalPaths', function ($pathQuery) use ($employeeId) {
                $pathQuery->where('employee_id', $employeeId) // Is assigned to me
                    ->whereColumn('step_order', 'asset_disposals.current_step'); // It is my turn
            }); // The overall process is still active
    }

    public function scopeMyRevisionApproval(Builder $query) {
        $employeeId = Auth::user()->employee->id;

        return $query->where('current_status', AssetDisposal::STATUS_REVISION)->where('requester_id', $employeeId);
    }
}
