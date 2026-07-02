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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique()->nullable();
            $table->string('no_ktp')->nullable();
            $table->string('fullname')->nullable();
            $table->string('email')->nullable();
            $table->string('addressktp')->nullable();
            $table->string('birthplace')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('gender')->nullable();
            $table->string('religion')->nullable();
            $table->string('marital')->nullable();
            $table->string('hp')->nullable();
            $table->date('joindate')->nullable();
            $table->date('enddate')->nullable();
            $table->string('status')->nullable();
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('area_id')->nullable(); 
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->unsignedBigInteger('level_id')->nullable();
            $table->unsignedBigInteger('building_id')->nullable();
            $table->string('work_location')->nullable();
            $table->string('avatar')->nullable();
            $table->date('contract_startdate')->nullable();
            $table->integer('contract_number')->nullable();
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('set null');
            $table->foreign('section_id')->references('id')->on('master_section')->onDelete('set null');
            $table->foreign('position_id')->references('id')->on('master_position')->onDelete('set null');
            $table->foreign('level_id')->references('id')->on('master_level')->onDelete('set null');
            $table->foreign('building_id')->references('id')->on('master_building')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
