<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('e_signs', function (Blueprint $table) {
            // Slot 1
            $table->unsignedBigInteger('employee1_signee_id')->nullable()->after('content')
                ->comment('Employee 1 — yang menandatangani');
            $table->timestamp('employee1_signed_at')->nullable()->after('employee1_signee_id')
                ->comment('Waktu tanda tangan Employee 1');
            $table->text('employee1_qr_code')->nullable()->after('employee1_signed_at')
                ->comment('QR Code Employee 1 dari API PSRE');
            $table->string('employee1_position_name', 255)->nullable()->after('employee1_qr_code')
                ->comment('Jabatan Employee 1 saat tanda tangan');

            // Slot 2
            $table->unsignedBigInteger('employee2_signee_id')->nullable()->after('employee1_position_name')
                ->comment('Employee 2 — yang menandatangani');
            $table->timestamp('employee2_signed_at')->nullable()->after('employee2_signee_id')
                ->comment('Waktu tanda tangan Employee 2');
            $table->text('employee2_qr_code')->nullable()->after('employee2_signed_at')
                ->comment('QR Code Employee 2 dari API PSRE');
            $table->string('employee2_position_name', 255)->nullable()->after('employee2_qr_code')
                ->comment('Jabatan Employee 2 saat tanda tangan');

            // Slot 3
            $table->unsignedBigInteger('employee3_signee_id')->nullable()->after('employee2_position_name')
                ->comment('Employee 3 — yang menandatangani');
            $table->timestamp('employee3_signed_at')->nullable()->after('employee3_signee_id')
                ->comment('Waktu tanda tangan Employee 3');
            $table->text('employee3_qr_code')->nullable()->after('employee3_signed_at')
                ->comment('QR Code Employee 3 dari API PSRE');
            $table->string('employee3_position_name', 255)->nullable()->after('employee3_qr_code')
                ->comment('Jabatan Employee 3 saat tanda tangan');

            // Foreign keys
            $table->foreign('employee1_signee_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('employee2_signee_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('employee3_signee_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('e_signs', function (Blueprint $table) {
            $table->dropForeign(['employee1_signee_id']);
            $table->dropForeign(['employee2_signee_id']);
            $table->dropForeign(['employee3_signee_id']);
            $table->dropColumn([
                'employee1_signee_id',
                'employee1_signed_at',
                'employee1_qr_code',
                'employee1_position_name',
                'employee2_signee_id',
                'employee2_signed_at',
                'employee2_qr_code',
                'employee2_position_name',
                'employee3_signee_id',
                'employee3_signed_at',
                'employee3_qr_code',
                'employee3_position_name',
            ]);
        });
    }
};
