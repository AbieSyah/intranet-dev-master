<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;
    protected $table = 'user_log';
    protected $fillable = ['user_id','ip_address','action','description'];

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
