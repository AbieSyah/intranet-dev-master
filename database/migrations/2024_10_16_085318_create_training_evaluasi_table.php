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
        Schema::create('training_evaluasi', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('id_training_record');
            $table->integer('dt_1');
            $table->integer('dt_2');
            $table->integer('dt_3');
            $table->integer('dt_4');
            $table->integer('dt_5');
            $table->integer('fap_1');
            $table->integer('fap_2');
            $table->integer('fap_3');
            $table->integer('fap_4');
            $table->integer('trainer_1');
            $table->integer('et_1');
            $table->integer('et_2');
            $table->integer('et_3');
            $table->integer('et_4');
            $table->integer('trainer_2');
            $table->integer('et_5');
            $table->integer('et_6');
            $table->integer('et_7');
            $table->integer('et_8');
            $table->integer('trainer_3');
            $table->integer('et_9');
            $table->integer('et_10');
            $table->integer('et_11');
            $table->integer('et_12');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_evaluasi');
    }
};
