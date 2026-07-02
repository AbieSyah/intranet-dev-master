<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTicketCC extends Model
{
    use HasFactory;

    protected $table = 'service_ticket_ccs';
    protected $primaryKey = 'id';
    protected $fillable = [
        'service_ticket_id',
        'employee_id',
    ];

    public function employee()
    {
        return parent::belongsTo(Employee::class, 'employee_id');
    }
}
