<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'description',
        'min_years',
        'max_years',
        'number_of_days'
    ];
}
