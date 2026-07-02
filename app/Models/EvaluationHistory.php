<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationHistory extends Model
{
    use HasFactory;
    protected $table = 'evaluation_histories';
    protected $fillable = [
        'evaluation_id',
        'user_id',
        'ip_address',
        'action',
        'description',
    ];
    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class, 'evaluation_id');
    }
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
