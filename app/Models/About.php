<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'user_id',
        'description',
        'release_date',
    ];

    protected $casts = [
        'release_date' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
