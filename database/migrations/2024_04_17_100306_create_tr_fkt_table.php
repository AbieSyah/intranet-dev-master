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
        Schema::create('training_fkt', function (Blueprint $table) {
            $table->id();
            $table->integer('id_pemohon');
            $table->string('tahun_usulan');
            $table->string('tahun_pelaksanaan');
            $table->string('tipe');
            $table->string('kode');
            $table->integer('id_peserta');
            $table->string('judul');
            $table->string('sifat');
            $table->string('alasan');
            $table->date('periode');
            $table->string('vendor');
            $table->string('penginapan');
            $table->string('transportasi');
            $table->integer('id_checker');
            $table->date('date_checker');
            $table->integer('id_verified');
            $table->date('date_verified');
            $table->integer('id_approval');
            $table->date('date_approval');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_fkt');
    }
};
