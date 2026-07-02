<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceStatusHistory extends Model
{
    use HasFactory;
    
    const STATUS_OPEN = 'open';
    const STATUS_PROCESS = 'process';
    const STATUS_HOLD = 'hold';
    const STATUS_CLOSED = 'closed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'from_status',
        'to_status',
        'note',
        'employee_id',
        'started_at',
        'resolved_at',
    ];

    protected $casts = [
        'started_at' => "datetime",
        'resolved_at' => "datetime",
    ];

    public function employee()
    {
        return parent::belongsTo(Employee::class, 'employee_id');
    }
}
