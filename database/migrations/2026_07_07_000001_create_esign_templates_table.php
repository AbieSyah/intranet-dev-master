<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('esign_templates', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_surat_slug', 50)->comment('Slug jenis surat (pkwt, promosi, dll)');
            $table->string('title', 255)->comment('Judul template');
            $table->longText('content')->nullable()->comment('Isi template dengan placeholder');
            $table->integer('version')->default(1)->comment('Versi template');
            $table->boolean('is_active')->default(true)->comment('Status aktif');
            $table->unsignedBigInteger('created_by')->nullable()->comment('User pembuat');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('User pengubah');
            $table->timestamps();

            // Indexes
            $table->index('jenis_surat_slug');
            $table->index('is_active');
            $table->index('version');

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('esign_templates');
    }
};
