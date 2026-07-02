<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;
    protected $table = 'master_level';
    protected $fillable = ['nama'];
    public $timestamps = true;

    public function employees(){
        return $this->hasMany(Employee::class, 'level_id');
    }
}
