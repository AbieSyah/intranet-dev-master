<?php

namespace App\Models;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class DisposalApprovalPath extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'employee_id',
        'asset_disposal_id',
        'position',
        'department',
        'email',
        'role_name',
        'step_order',
        'is_approved',
    ];

    public function assetDisposal()
    {
        return parent::belongsTo(AssetDisposal::class);
    }

    public function logs()
    {
        return parent::hasMany(AssetDisposalLog::class);
    }

    public function employee()
    {
        return parent::belongsTo(Employee::class);
    }
}
