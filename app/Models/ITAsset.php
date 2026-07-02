<?php

namespace App\Models;

use App\Models\Area;
use App\Models\AssetDisposal;
use App\Models\AssetDisposalItem;
use App\Models\AssetType;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ITAsset extends Model
{
    use HasFactory;

    protected $table = 'it_assets';
    protected $fillable = [
        'asset_code',
        'brand',
        'status',
        'asset_type_id',
        'year_registered',
        'price',
        'specification',
        'software',
        'employee_id',
        'employee_fullname',
        'employee_nik',
        'employee_department',
        'employee_position',
        'employee_area',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_BROKEN = 'broken';
    const STATUS_DISPOSED = 'disposed';
    const STATUS_ON_DISPOSAL = 'on_disposal';
    const STATUS_BACKUP = 'backup';

    protected $casts = [
        // This converts the string/date from DB into a Carbon instance
        'year_registered' => 'date', 
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function assetType()
    {
        return $this->belongsTo(AssetType::class);
    }

    public function histories() {
        return $this->hasMany(AssetHistory::class, 'it_asset_id', 'id');
    }

    public function scopeCurrentActiveDisposal()
    {
        // dd($itAsset);
        // return AssetDisposal::whereHas('disposalItems', function($query) use ($itAsset) {
        //     $query->where('it_asset_id', $itAsset->id);
        // })->where('doc_status', AssetDisposal::DOC_STATUS_DRAFT);
        return $this->disposalItems
            ->map(fn($item) => $item->assetDisposal) // Get the parent objects
            ->filter(fn($parent) => $parent->doc_status == AssetDisposal::DOC_STATUS_DRAFT) // Filter by your "active" logic
            ->unique('id');
    }

    public function scopeLatestDisposalItem() {
        return $this->disposalItems->sortBy('created_at')->first();
    }

    public function disposalItems()
    {
        return $this->hasMany(AssetDisposalItem::class, 'it_asset_id', 'id');
    }

    public function maintenances() {
        return $this->hasMany(Maintenance::class, 'it_asset_id', 'id');
    }

    public function serviceTickets() {
        return $this->belongsToMany(ServiceTicket::class, ServiceTicketAsset::class, 'it_asset_id', 'service_ticket_id')->withPivot('employee_id');
    }
}
