<?php

namespace App\Models\Security;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class Guest extends Model
{
    use HasFactory;

    protected $table = 'security_guest';

    protected $fillable = [
        'nama',
        'alamat_pribadi',
        'nomor_visitor',
        'nomor_kartu_identitas',
        'perusahaan',
        'alamat_perusahaan',
        'tujuan_kunjungan',
        'nama_pic',
        'id_employee',
        'resiko_kesehatan',
        'nomor_visitor',
        'suhu',
        'q1',
        'q2',
        'q3',
        'q4',
        'q5',
        'q6',
        'lama_kunjungan',
        'jenis_kendaraan',
        'muatan_kendaraan',
        'nomor_polisi',
        'waktu_keluar',
        'waktu_bertemu'
    ];

    protected $casts = [
        'waktu_keluar' => 'datetime',
        'waktu_bertemu' => 'datetime',
        'tanggal' => 'date',
        'q1' => 'boolean',
        'q2' => 'boolean',
        'q3' => 'boolean',
        'q4' => 'boolean',
        'q5' => 'boolean',
        'q6' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'id_employee');
    }
}
