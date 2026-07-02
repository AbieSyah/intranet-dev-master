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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('appraisal_id');
            
            $table->unsignedBigInteger('appraisal_position_id');
            $table->string('appraisal_status');

            $table->unsignedBigInteger('approval1_id')->nullable();
            $table->unsignedBigInteger('approval2_id')->nullable();
            $table->unsignedBigInteger('approval3_id')->nullable();
            $table->unsignedBigInteger('approval4_id')->nullable();
            $table->unsignedBigInteger('approval5_id')->nullable();
            $table->unsignedBigInteger('approval6_id')->nullable();
            $table->unsignedBigInteger('drafter_id')->nullable();

            $table->string('approval1_as')->nullable();
            $table->string('approval2_as')->nullable();
            $table->string('approval3_as')->nullable();
            $table->string('approval4_as')->nullable();
            $table->string('approval5_as')->nullable();
            $table->string('approval6_as')->nullable();

            $table->date('eval_start');
            $table->date('eval_end');
            $table->string('purpose');

            $table->integer('kpi_w')->nullable();
            $table->decimal('kpi_s')->nullable();
            $table->decimal('kpi_sc')->nullable();
            $table->string('kpi_c')->nullable();

            $table->integer('ap_managerial_w')->nullable();
            $table->decimal('ap_managerial_s')->nullable();
            $table->decimal('ap_managerial_sc')->nullable();
            $table->string('ap_managerial_c')->nullable();

            $table->integer('ap_ability_response_w')->nullable();
            $table->decimal('ap_ability_response_s')->nullable();
            $table->decimal('ap_ability_response_sc')->nullable();
            $table->string('ap_ability_response_c')->nullable();

            $table->integer('ap_leadership_w')->nullable();
            $table->decimal('ap_leadership_s')->nullable();
            $table->decimal('ap_leadership_sc')->nullable();
            $table->string('ap_leadership_c')->nullable();

            $table->integer('ap_accuracy_w')->nullable();
            $table->decimal('ap_accuracy_s')->nullable();
            $table->decimal('ap_accuracy_sc')->nullable();
            $table->string('ap_accuracy_c')->nullable();

            $table->integer('ap_capability_w')->nullable();
            $table->decimal('ap_capability_s')->nullable();
            $table->decimal('ap_capability_sc')->nullable();
            $table->string('ap_capability_c')->nullable();

            $table->integer('ap_initiative_w')->nullable();
            $table->decimal('ap_initiative_s')->nullable();
            $table->decimal('ap_initiative_sc')->nullable();
            $table->string('ap_initiative_c')->nullable();

            $table->integer('ap_kaizen_w')->nullable();
            $table->decimal('ap_kaizen_s')->nullable();
            $table->decimal('ap_kaizen_sc')->nullable();
            $table->string('ap_kaizen_c')->nullable();

            $table->integer('ap_responsibility_w')->nullable();
            $table->decimal('ap_responsibility_s')->nullable();
            $table->decimal('ap_responsibility_sc')->nullable();
            $table->string('ap_responsibility_c')->nullable();

            $table->integer('ap_discipline_w')->nullable();
            $table->decimal('ap_discipline_s')->nullable();
            $table->decimal('ap_discipline_sc')->nullable();
            $table->string('ap_discipline_c')->nullable();

            $table->integer('ap_cooperation_w')->nullable();
            $table->decimal('ap_cooperation_s')->nullable();
            $table->decimal('ap_cooperation_sc')->nullable();
            $table->string('ap_cooperation_c')->nullable();

            $table->integer('ap_w')->nullable();
            $table->decimal('ap_s')->nullable();
            $table->decimal('ap_sc')->nullable();

            $table->integer('attendance_w')->nullable();
            $table->decimal('attendance_s')->nullable();
            $table->decimal('attendance_sc')->nullable();
            $table->string('attendance_c')->nullable();

            $table->decimal('minus_poin')->nullable();
            $table->decimal('total_score')->nullable();
            $table->char('grade', 1)->nullable();
            $table->text('positive')->nullable();
            $table->text('weakness')->nullable();
            $table->text('note_hrd')->nullable();
            $table->string('decision_employment')->nullable();
            $table->string('month_extend')->nullable();
            $table->date('date_extend')->nullable();
            $table->string('decision_reason')->nullable();
            $table->string('status');
            
            $table->string('release_id')->nullable()->unique();
            $table->dateTime('release_date')->nullable();

            $table->dateTime('approval1_date')->nullable();
            $table->dateTime('approval2_date')->nullable();
            $table->dateTime('approval3_date')->nullable();
            $table->dateTime('approval4_date')->nullable();
            $table->dateTime('approval5_date')->nullable();
            $table->dateTime('approval6_date')->nullable();
            $table->dateTime('drafter_date')->nullable();

            $table->string('approval1_reason')->nullable();
            $table->string('approval2_reason')->nullable();
            $table->string('approval3_reason')->nullable();
            $table->string('approval4_reason')->nullable();
            $table->string('approval5_reason')->nullable();
            $table->string('approval6_reason')->nullable();

            $table->timestamps();
        });

        Schema::create('evaluation_histories', function (Blueprint $table) {
           $table->id();
            $table->unsignedBigInteger('evaluation_id');
            $table->unsignedBigInteger('user_id');
            $table->string('ip_address');
            $table->string('action');
            $table->text('description');
            $table->timestamps();
            $table->foreign('evaluation_id')->references('id')->on('evaluations')->onDelete('cascade');
        });

        Schema::create('evaluation_attachments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->timestamps();
        });

        Schema::create('evaluation_has_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evaluation_id');
            $table->unsignedBigInteger('attachment_id');
            $table->foreign('evaluation_id')->references('id')->on('evaluations')->onDelete('cascade');
            $table->foreign('attachment_id')->references('id')->on('evaluation_attachments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_has_attachments');
        Schema::dropIfExists('evaluation_attachments');
        Schema::dropIfExists('evaluation_histories');
        Schema::dropIfExists('evaluations');
    }
};