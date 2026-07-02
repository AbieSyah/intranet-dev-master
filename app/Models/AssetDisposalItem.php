<?php

namespace App\Models;

use App\Models\AssetDisposal;
use App\Models\ITAsset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetDisposalItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'asset_disposal_id',
        'it_asset_id',
        'buy_price',
        'sale_price',
        'current_status', // reference into ITAsset::STATUS_*
        'reason',
    ];

    public function itAsset()
    {
        return $this->belongsTo(ITAsset::class);
    }

    public function assetDisposal()
    {
        return $this->belongsTo(AssetDisposal::class);
    }
}
