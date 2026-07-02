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
        Schema::create('candidate', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('posting_id');
            $table->unsignedBigInteger('position_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('area_id')->nullable();

            $table->string('no_ktp')->nullable();
            $table->string('fullname')->nullable();
            $table->string('nickname')->nullable();
            $table->string('ktp_address')->nullable();
            $table->string('domicile_address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('birthplace')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('gender')->nullable();
            $table->string('religion')->nullable();
            $table->string('marital')->nullable();
            $table->smallInteger('height')->nullable();
            $table->smallInteger('weight')->nullable();
            $table->text('skill')->nullable();
            $table->string('expected_salary')->nullable();
            $table->dateTime('submit_date')->nullable();
            $table->string('photo')->nullable();
            // Log
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('referer_source')->nullable();
            $table->timestamp('captcha_verified_at')->nullable();

            $table->timestamps();
        });

        Schema::create('candidate_experience', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_id');
            $table->foreign('candidate_id')->references('id')->on('candidate')->onDelete('cascade');
            $table->string('company')->nullable();
            $table->string('position')->nullable();
            $table->smallInteger('years')->nullable();
            $table->index('candidate_id');
            $table->timestamps();
        });

        Schema::create('candidate_education', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_id');
            $table->foreign('candidate_id')->references('id')->on('candidate')->onDelete('cascade');
            $table->string('level')->nullable();
            $table->string('institution_name')->nullable();
            $table->string('major')->nullable();
            $table->year('year_graduated')->nullable();
            $table->decimal('score_gpa', 5, 2)->nullable();
            $table->string('ijazah')->nullable();
            $table->index('candidate_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_education');
        Schema::dropIfExists('candidate_experience');
        Schema::dropIfExists('candidate');
    }
};
