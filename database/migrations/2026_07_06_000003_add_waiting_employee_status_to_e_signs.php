<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE e_signs MODIFY COLUMN status ENUM(
            'draft', 'pending', 'approved', 'rejected',
            'waiting_employee',
            'approved_employee', 'rejected_employee'
        ) NOT NULL DEFAULT 'draft' COMMENT 'Status E-Sign'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE e_signs MODIFY COLUMN status ENUM(
            'draft', 'pending', 'approved', 'rejected',
            'approved_employee', 'rejected_employee'
        ) NOT NULL DEFAULT 'draft' COMMENT 'Status E-Sign'");
    }
};
