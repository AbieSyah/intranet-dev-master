<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTicketAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_ticket_id',
        'employee_id',
        'it_asset_id',
    ];

}
