<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTicketMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'role',
        'message',
        'is_internal',
    ];

    public const ROLE_SYSTEM = 'system';
    public const ROLE_USER = 'user';
    public const ROLE_IT = 'it';
    public const ROLE_SERVICE_CHANGE = 'service_change';

    public function ticket()
    {
        return parent::belongsTo(ServiceTicket::class, 'service_ticket_id', 'id');
    }

    public function media() {
        return parent::hasMany(ServiceTicketMedia::class);
    }

    public function sender() {
        return parent::belongsTo(Employee::class, 'sender_id', 'id');
    }
}
