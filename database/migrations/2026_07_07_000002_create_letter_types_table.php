<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letter_types', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique()->comment('Slug untuk identifikasi unik');
            $table->string('name', 100)->comment('Nama jenis surat');
            $table->string('prefix', 20)->comment('Prefix nomor surat (PKWT, PROM, dll)');
            $table->text('description')->nullable()->comment('Deskripsi jenis surat');
            $table->string('icon', 50)->default('ri-file-text-line')->comment('Icon Remixicon');
            $table->string('color', 20)->default('primary')->comment('Warna badge');
            $table->boolean('is_active')->default(true)->comment('Status aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_types');
    }
};
