<?php

namespace App\Models;

use App\Models\AssetDisposal;
use App\Models\DisposalApprovalPath;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetDisposalLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_disposal_id',
        'disposal_approval_path_id',
        'status',
        'for_buyer',
        'comments',
        'actioned_at'
    ];

    protected $casts = [
        'for_buyer' => 'boolean',
        'actioned_at' => 'datetime'
    ];

    protected static function booted()
    {
        // Whenever a new status record is created...
        static::created(function ($assetDisposalLog) {
            $assetDisposalLog->syncToAssetDisposal();
        });

        // Optional: If you allow editing existing status records
        static::updated(function ($assetDisposalLog) {
            $assetDisposalLog->syncToAssetDisposal();
        });

        static::deleted(function ($assetDisposalLog) {
            $latest = AssetDisposalLog::where('asset_disposal_id', $assetDisposalLog->asset_disposal_id)
                ->latest()
                ->first();
                
            $assetDisposalLog->assetDisposal()->update([
                'current_status' => $latest ? $latest->status : 'No Status'
            ]);
        });
    }

    public function syncToAssetDisposal()
    {
        $this->assetDisposal()->update([
            'current_status' => $this->status
        ]);
    }

    public function approvalPath()
    {
        return parent::belongsTo(DisposalApprovalPath::class, 'disposal_approval_path_id', 'id');
    }

    public function assetDisposal() {
        return parent::belongsTo(AssetDisposal::class);
    }

    public function approver()
    {
        return parent::hasOneThrough(
            Employee::class, 
            DisposalApprovalPath::class, 
            'id', 
            'id', 
            'disposal_approval_path_id', 
            'employee_id',
            );
    }
}
