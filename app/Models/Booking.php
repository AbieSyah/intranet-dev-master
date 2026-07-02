<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $table = 'booking_record';
    protected $fillable = ['brief_description','full_description','date_start','date_end','room_id','tipe','status','kode','employee_id','repeat_day','repeat_week','repeat_month','repeat_status'];

    public function room() {
        return $this->belongsTo('App\Models\Room', 'room_id', 'id');
    }
    public function employee() {
        return $this->belongsTo('App\Models\Employee', 'employee_id', 'id');
    }
}
