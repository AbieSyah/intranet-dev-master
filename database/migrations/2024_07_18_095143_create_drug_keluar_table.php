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
        Schema::create('drug_keluar', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kategori', 255);
            $table->string('pasien_code', 255);
            $table->integer('id_employee');
            $table->date('tr_tanggal');
            $table->integer('id_drug');
            $table->integer('jml_drug');
            $table->text('ket');
            $table->integer('id_user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drug_keluar');
    }
};
