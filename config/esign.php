<?php

return [

    /*
    |--------------------------------------------------------------------------
    | E-Sign Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk fitur E-Sign Management.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Nomor Surat Prefixes
    |--------------------------------------------------------------------------
    |
    | Prefix yang digunakan untuk generate nomor surat otomatis
    | berdasarkan jenis_surat_slug.
    |
    | Format nomor surat: {PREFIX}/{TAHUN}/{URUTAN}
    | Contoh: PKWT/2026/001
    |
    | Jika slug tidak terdaftar, prefix fallback: DOC
    |
    */
    'prefixes' => [
        'pkwt' => 'PKWT',
        'promosi' => 'PROMOSI',
        'mutasi' => 'MUTASI',
        'demosi' => 'DEMOSI',
        'perpanjangan-pkwt' => 'PPKWT',
        'pengangkatan' => 'ANGKAT',
        'surat-peringatan' => 'SP',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix default jika jenis_surat_slug tidak ditemukan di daftar prefixes.
    |
    */
    'fallback_prefix' => 'DOC',

    /*
    |--------------------------------------------------------------------------
    | Template Placeholders
    |--------------------------------------------------------------------------
    |
    | Daftar placeholder yang dapat digunakan di dalam template surat.
    | Key = placeholder name (tanpa kurung), value = deskripsi.
    |
    */
    'placeholders' => [
        // Employee 1
        'employee_name' => 'Nama Karyawan',
        'employee_nik' => 'NIK',
        'employee_position' => 'Jabatan',
        'employee_department' => 'Departemen',
        'employee_birthplace' => 'Tempat Lahir',
        'employee_birthdate' => 'Tanggal Lahir',
        'employee_gender' => 'Jenis Kelamin',
        'employee_religion' => 'Agama',
        'employee_marital' => 'Status Perkawinan',
        'employee_hp' => 'No. HP',
        'employee_email' => 'Email',

        // Employee 2
        'employee2_name' => 'Nama Karyawan 2',
        'employee2_nik' => 'NIK 2',
        'employee2_position' => 'Jabatan 2',
        'employee2_department' => 'Departemen 2',
        'employee2_birthplace' => 'Tempat Lahir 2',
        'employee2_birthdate' => 'Tanggal Lahir 2',
        'employee2_gender' => 'Jenis Kelamin 2',
        'employee2_religion' => 'Agama 2',
        'employee2_marital' => 'Status Perkawinan 2',
        'employee2_hp' => 'No. HP 2',
        'employee2_email' => 'Email 2',

        // Employee 3
        'employee3_name' => 'Nama Karyawan 3',
        'employee3_nik' => 'NIK 3',
        'employee3_position' => 'Jabatan 3',
        'employee3_department' => 'Departemen 3',
        'employee3_birthplace' => 'Tempat Lahir 3',
        'employee3_birthdate' => 'Tanggal Lahir 3',
        'employee3_gender' => 'Jenis Kelamin 3',
        'employee3_religion' => 'Agama 3',
        'employee3_marital' => 'Status Perkawinan 3',
        'employee3_hp' => 'No. HP 3',
        'employee3_email' => 'Email 3',

        // Document
        'tanggal_mulai' => 'Tanggal Mulai',
        'tanggal_akhir' => 'Tanggal Berakhir',
        'judul_surat' => 'Judul Surat',

        // Signatures
        'sign_employee1' => 'Tanda Tangan Employee 1',
        'sign_employee2' => 'Tanda Tangan Employee 2',
        'sign_employee3' => 'Tanda Tangan Employee 3',

        // System
        'today' => 'Tanggal Hari Ini',
    ],

];
