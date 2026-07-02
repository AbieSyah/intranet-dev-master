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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('bpjs_kesehatan')->nullable();
            $table->string('bpjs_ketenagakerjaan')->nullable();
            $table->string('latest_agreement_number')->nullable();
            $table->string('active_agreement_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_account_holder')->nullable();
            $table->string('blood_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'bpjs_kesehatan',
                'bpjs_ketenagakerjaan',
                'latest_agreement_number',
                'active_agreement_number',
                'bank_name',
                'bank_account',
                'bank_account_holder',
                'blood_type'
            ]);
        });
    }
};
