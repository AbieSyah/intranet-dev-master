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
        Schema::create('master_appraisal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('position_id');
            $table->string('status');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('section_id')->nullable();
            $table->char('form_type', 1);
            $table->integer('kpi_weight');
            $table->integer('ap_weight');
            $table->integer('ap_managerial');
            $table->integer('ap_ability_response');
            $table->integer('ap_leadership');
            $table->integer('ap_accuracy');
            $table->integer('ap_capability');
            $table->integer('ap_initiative');
            $table->integer('ap_kaizen');
            $table->integer('ap_responsibility');
            $table->integer('ap_discipline');
            $table->integer('ap_cooperation');
            $table->integer('ap_total');
            $table->integer('attendance');
            $table->integer('total');
            $table->timestamps();

            $table->foreign('position_id')->references('id')->on('master_position');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('section_id')->references('id')->on('master_section');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_appraisal');
    }
};
