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
        Schema::create('selection_process', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requisition_id');
            $table->unsignedBigInteger('requisition_hiring_step_id');
            $table->string('location')->nullable(); 
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->tinyInteger('status')
                ->default(0)
                ->comment('0=Draft; 1=Release; 2=Done;');
            $table->string('noted')->nullable();
            $table->timestamps();

            $table->foreign('requisition_id')->references('id')->on('employee_requisition')->onDelete('cascade');
            $table->foreign('requisition_hiring_step_id', 'sel_req_step_fk')->references('id')->on('employee_requisition_hiring_steps')->onDelete('cascade');
        });

        Schema::create('selection_process_candidates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_id');
            $table->unsignedBigInteger('selection_process_id');
            $table->dateTime('email_notification_sent_at')->nullable();
            $table->boolean('is_present')
                ->default(false)
                ->comment('0=Absent; 1=Present');
            $table->tinyInteger('result_status')
                ->default(0)
                ->comment('0=Scheduled; 1=Passed; 2=Failed; 3=Done');
            $table->text('comment')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
            
            $table->foreign('candidate_id')->references('id')->on('candidate')->onDelete('cascade');
            $table->foreign('selection_process_id', 'sel_cand_proc_fk')->references('id')->on('selection_process')->onDelete('cascade');
            $table->unique(['candidate_id', 'selection_process_id'], 'sel_cand_unique');
        });

        Schema::create('selection_process_employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('selection_process_id');
            $table->unsignedBigInteger('employee_id');
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('selection_process_id', 'sel_emp_proc_fk')->references('id')->on('selection_process')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->unique(['selection_process_id', 'employee_id'], 'sel_emp_unique');
        });

        Schema::create('selection_process_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sel_process_candidate_id');
            $table->unsignedBigInteger('employee_id');
            $table->tinyInteger('result_status')
                ->default(0)
                ->comment('0=Scheduled; 1=Passed; 2=Failed');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('sel_process_candidate_id', 'assess_cand_fk')->references('id')->on('selection_process_candidates')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->unique(['sel_process_candidate_id', 'employee_id'], 'assess_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selection_process_assessments');
        Schema::dropIfExists('selection_process_employees');
        Schema::dropIfExists('selection_process_candidates');
        Schema::dropIfExists('selection_process');
    }
};
