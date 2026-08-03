<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom yang diperlukan untuk fitur surat (CRUD surat)
     * ke tabel e_signs yang sudah ada.
     */
    public function up(): void
    {
        Schema::table('e_signs', function (Blueprint $table) {
            // Kolom nomor surat — format: PREFIX/TAHUN/SEQUENTIAL (e.g. PKWT/2026/001)
            $table->string('nomor_surat', 50)
                ->after('employee_id')
                ->comment('Nomor surat otomatis, format: PREFIX/TAHUN/NOMOR_URUT');

            // Slug jenis surat — merujuk ke letter type dari ESignDummyData
            // Values: pkwt, promosi, mutasi, demosi, perpanjangan-pkwt, pengangkatan, surat-peringatan
            $table->string('jenis_surat_slug', 50)
                ->after('document_name')
                ->comment('Slug jenis surat (pkwt, promosi, mutasi, demosi, dll)');

            // Tanggal berlaku surat
            $table->date('tanggal_mulai')
                ->after('upload_date')
                ->comment('Tanggal berlaku surat');

            // Tanggal berakhir surat (nullable — untuk surat tanpa masa berlaku)
            $table->date('tanggal_akhir')
                ->nullable()
                ->after('tanggal_mulai')
                ->comment('Tanggal berakhir surat (nullable)');

            // Index untuk query filter by jenis surat
            $table->index('jenis_surat_slug', 'idx_e_signs_jenis_surat_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('e_signs', function (Blueprint $table) {
            $table->dropIndex('idx_e_signs_jenis_surat_slug');
            $table->dropColumn([
                'nomor_surat',
                'jenis_surat_slug',
                'tanggal_mulai',
                'tanggal_akhir',
            ]);
        });
    }
};
