<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('training_fpkt', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->string('latar_belakang');
            $table->string('tujuan');
            $table->string('kompetensi');
            $table->string('skill');
            $table->integer('level_peserta');
            $table->integer('level_atasan');
            $table->double('level_rata');
            $table->string('level_kebutuhan');
            $table->string('catatan');
            $table->integer('id_peserta');
            $table->date('date_peserta');
            $table->integer('id_atasan');
            $table->date('date_atasan');
            $table->integer('id_hrd');
            $table->date('date_hrd');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_fpkt');
    }
};
