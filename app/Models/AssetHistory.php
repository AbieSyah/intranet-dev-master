<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'it_asset_id',
        'action_type',
        'from_value',
        'to_value',
        'reference_type',
        'reference_id',
        'description',
        'user_id',
    ];

    // created, movement(owner_changed), status_changed, replaced, disposed

    const TYPE_CREATE = 'create';
    const TYPE_MOVEMENT = 'movement';
    const TYPE_STATUS_CHANGE = 'status_change';
    const TYPE_REPLACE = 'replace';
    const TYPE_DISPOSED = 'disposed';
    const TYPE_DISPOSAL_REQUEST = 'disposal_request';

    const REFERENCE_DISPOSAL = AssetDisposal::class;
    // const REFERENCE_TICKET = 'ticket';
    // const REFERENCE_CHANGE = 'change';
    // const REFERENCE_SYSTEM = 'system';

    public function reference() {
        return $this->morphTo();
    }
}
