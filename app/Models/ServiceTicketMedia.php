<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTicketMedia extends Model
{
    use HasFactory;
    protected $fillable = [
        'path',
        'name',
        'extension'
    ];

    public function message() {
        return $this->belongsTo(ServiceTicketMessage::class, 'service_ticket_message_id');
    }
}
