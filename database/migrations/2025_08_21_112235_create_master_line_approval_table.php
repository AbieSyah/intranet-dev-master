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
        Schema::create('master_line_approval', function (Blueprint $table) {
            $table->id();
            $table->string('approval_type');
            $table->string('group_name')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('area_id')->nullable();
            $table->unsignedBigInteger('building_id')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('approve_1')->nullable();
            $table->unsignedBigInteger('approve_2')->nullable();
            $table->unsignedBigInteger('approve_3')->nullable();
            $table->unsignedBigInteger('approve_4')->nullable();
            $table->unsignedBigInteger('approve_5')->nullable();
            $table->unsignedBigInteger('approve_6')->nullable();
            $table->unsignedBigInteger('approve_7')->nullable();
            $table->unsignedBigInteger('approve_8')->nullable();
            $table->unsignedBigInteger('drafter')->nullable();
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('set null');
            $table->foreign('building_id')->references('id')->on('master_building')->onDelete('set null');
            $table->foreign('position_id')->references('id')->on('master_position')->onDelete('set null');
            $table->foreign('section_id')->references('id')->on('master_section')->onDelete('set null');
            $table->foreign('approve_1')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('approve_2')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('approve_3')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('approve_4')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('approve_5')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('approve_6')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('approve_7')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('approve_8')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('drafter')->references('id')->on('employees')->onDelete('set null');
        });

        Schema::create('master_line_approval_employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('line_approval_id');
            $table->unsignedBigInteger('employee_id');
            $table->timestamps();
            $table->foreign('line_approval_id')->references('id')->on('master_line_approval')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_line_approval_employees');
        Schema::dropIfExists('master_line_approval');
    }
};
