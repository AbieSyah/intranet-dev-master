<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan status approved_employee dan rejected_employee ke ENUM status.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE e_signs MODIFY COLUMN status ENUM(
            'draft', 'pending', 'approved', 'rejected',
            'approved_employee', 'rejected_employee'
        ) NOT NULL DEFAULT 'draft' COMMENT 'Status E-Sign'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE e_signs MODIFY COLUMN status ENUM(
            'draft', 'pending', 'approved', 'rejected'
        ) NOT NULL DEFAULT 'draft' COMMENT 'Status E-Sign'");
    }
};
