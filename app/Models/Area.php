<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
    protected $fillable = ['kode','name'];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'area');
    }
}
