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
        Schema::create('training_record', function (Blueprint $table) {
            $table->id();
            $table->integer('id_employee');
            $table->string('judul');
            $table->string('detail');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('vendor');
            $table->string('lokasi');
            $table->string('biaya');
            $table->string('sertifikat');
            $table->string('exp_date');
            $table->integer('id_fkt');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_record');
    }
};
