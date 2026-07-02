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
        Schema::create('job_posting', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requisition_id');
            $table->unsignedBigInteger('position_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('area_id')->nullable();
            $table->string('status');
            $table->string('title')->nullable();
            $table->text('qualification')->nullable();
            $table->integer('needs')->nullable();
            $table->string('employee_status')->nullable();
            $table->string('publish_id')->nullable()->unique();
            $table->string('publish_code')->nullable()->unique();
            $table->dateTime('publish_date')->nullable();
            $table->date('apply_start')->nullable();
            $table->date('apply_end')->nullable();
            $table->string('noted')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_posting');
    }
};
