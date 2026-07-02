<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'month',
        'day',
        'it_asset_id',
        'owner_id',
        'department',
        'building',
        'area',
    ];

    protected $appends = ['full_date'];

    protected function fullDate(): Attribute
    {
        return Attribute::get(function () {
            return Carbon::create($this->year, $this->month, $this->day)->toDateString();
        });
    }

    public function asset()
    {
        return parent::belongsTo(ITAsset::class, 'it_asset_id');
    }

    public function owner()
    {
        return parent::belongsTo(Employee::class, 'owner_id');
    }
}