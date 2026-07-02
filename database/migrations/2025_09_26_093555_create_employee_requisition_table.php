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
        Schema::create('employee_requisition', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('applicant_id');
            $table->unsignedBigInteger('position_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('area_id')->nullable();
            
            $table->integer('needs')->nullable();
            $table->string('reason_requisition')->nullable();
            $table->unsignedBigInteger('person_replaced_id')->nullable();
            $table->string('reason_replacement')->nullable();
            $table->string('reason_replacement_other')->nullable();
            $table->string('employee_status')->nullable();
            $table->string('contract_period')->nullable();
            
            $table->string('work_experience')->nullable();
            $table->integer('duration_work_experience')->nullable();
            $table->text('qualification')->nullable();
            $table->date('employment_date')->nullable();
            $table->string('decision')->nullable();
            $table->string('decision_comment')->nullable();
            $table->string('status')->nullable();
            
            $table->string('no_pengajuan')->nullable()->unique();
            $table->dateTime('submit_date')->nullable();

            $table->unsignedBigInteger('approval1_id')->nullable();
            $table->unsignedBigInteger('approval2_id')->nullable();
            $table->unsignedBigInteger('approval3_id')->nullable();
            $table->unsignedBigInteger('approval4_id')->nullable();

            $table->string('approval1_as')->nullable();
            $table->string('approval2_as')->nullable();
            $table->string('approval3_as')->nullable();
            $table->string('approval4_as')->nullable();

            $table->dateTime('approval1_date')->nullable();
            $table->dateTime('approval2_date')->nullable();
            $table->dateTime('approval3_date')->nullable();
            $table->dateTime('approval4_date')->nullable();

            $table->dateTime('fulfilled_date')->nullable();
            $table->string('fulfilled_reason')->nullable();

            $table->timestamps();
        });

        Schema::create('employee_requisition_educations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('employee_requisition_has_educations', function (Blueprint $table) {
            $table->unsignedBigInteger('requisition_id');
            $table->unsignedBigInteger('education_id');
            $table->string('major')->nullable();
            $table->foreign('requisition_id')->references('id')->on('employee_requisition')->onDelete('cascade');
            $table->foreign('education_id')->references('id')->on('employee_requisition_educations')->onDelete('cascade');
            $table->primary(['requisition_id', 'education_id'], 'req_edu_primary');
        });

        Schema::create('employee_requisition_genders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requisition_id');
            $table->string('gender_name');
            $table->integer('needs_count');
            $table->integer('start_age');
            $table->integer('end_age');
            $table->foreign('requisition_id')->references('id')->on('employee_requisition')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('employee_requisition_recruitment_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('employee_requisition_has_recruitment_sources', function (Blueprint $table) {
            $table->unsignedBigInteger('requisition_id');
            $table->unsignedBigInteger('source_id');
            $table->string('other_detail')->nullable();
            $table->foreign('requisition_id', 'req_source_req_fk')->references('id')->on('employee_requisition')->onDelete('cascade');
            $table->foreign('source_id', 'req_source_source_fk')->references('id')->on('employee_requisition_recruitment_sources')->onDelete('cascade');
            $table->primary(['requisition_id', 'source_id'], 'req_source_primary');
        });

        Schema::create('employee_requisition_hiring_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requisition_id');
            $table->unsignedBigInteger('master_hiring_id');
            $table->tinyInteger('step_order');
            $table->timestamps();

            $table->foreign('requisition_id')->references('id')->on('employee_requisition')->onDelete('cascade');
            $table->foreign('master_hiring_id')->references('id')->on('master_hiring')->onDelete('cascade');
            $table->unique(['requisition_id', 'step_order'], 'req_step_order_unique'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_requisition_hiring_steps');
        Schema::dropIfExists('employee_requisition_has_educations');
        Schema::dropIfExists('employee_requisition_has_recruitment_sources');
        Schema::dropIfExists('employee_requisition_genders');
        Schema::dropIfExists('employee_requisition_educations');
        Schema::dropIfExists('employee_requisition_recruitment_sources');
        Schema::dropIfExists('employee_requisition');
    }
};
