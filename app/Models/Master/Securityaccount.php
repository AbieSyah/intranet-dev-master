<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Securityaccount extends Model
{
    use HasFactory;
    protected $table = 'security_account';
    protected $fillable = ['id_security','nama'];
    public $timestamps = true;
}
