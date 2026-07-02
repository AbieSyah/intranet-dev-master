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
        Schema::create('patient', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('visit_date');
            $table->integer('id_dokter');
            $table->integer('id_employee');
            $table->text('keluhan');
            $table->text('diagnosa');
            $table->text('tensi');
            $table->string('kode', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient');
    }
};
