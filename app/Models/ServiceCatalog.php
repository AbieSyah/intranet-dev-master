<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCatalog extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'service_catalog',
        'description',
    ];

    const CATEGORY_BUSINESS_APP = 'aplikasi_bisnis';
    const CATEGORY_COMMUNICATION = 'komunikasi';
    const CATEGORY_INFRASTRUCTURE = 'infrastruktur';
    const CATEGORY_HARDWARE = 'hardware';
    const CATEGORY_SOFTWARE = 'software';
}
