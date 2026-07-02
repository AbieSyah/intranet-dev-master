<?php

use App\Models\Employee;
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
        Schema::create('asset_disposals', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->nullable()->unique(); // e.g., DISP-2026-0001
            $table->foreignIdFor(Employee::class, 'requester_id')->constrained('employees')->restrictOnDelete();
            // $table->string('title');

            $table->string('buyer_name')->nullable();
            $table->string('buyer_phone')->nullable();
            $table->string('buyer_email')->nullable();
            $table->string('buyer_address')->nullable();
            $table->boolean('buyer_confirmed')->default(false);
            $table->datetime('validated_at')->nullable()->comment("this date time is for buyer validation time");
            $table->string('buyer_ip')->nullable();

            $table->text('reason')->nullable();
            // $table->decimal('total_price', 15, 2)->default(0); // Sum of all assets
            $table->integer('current_step')->nullable()->default(1); // pointer to diposalApprovalPath to indicate at wich state the approval is
            $table->string('doc_status')->default('draft'); // status of proposal document. Draft and Approved
            $table->string('current_status')->default('waiting'); // the current status of the proposal. Waiting, approve, revise, rejected
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_disposals');
    }
};
