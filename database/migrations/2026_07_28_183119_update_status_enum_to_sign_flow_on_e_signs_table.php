<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Ubah ENUM dulu — tambah status baru
        DB::statement("ALTER TABLE e_signs MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'draft' COMMENT 'Status E-Sign'");

        // Step 2: Ubah status lama ke status baru sesuai flow sign berurutan
        // Map: pending/waiting_employee → sign_1
        DB::statement("UPDATE e_signs SET status = 'sign_1' WHERE status IN ('pending', 'waiting_employee')");

        // Map: approved_employee → completed (sudah ditandatangani)
        DB::statement("UPDATE e_signs SET status = 'completed' WHERE status = 'approved_employee'");

        // Map: approved → completed
        DB::statement("UPDATE e_signs SET status = 'completed' WHERE status = 'approved'");

        // Hapus status yang tidak dipakai
        DB::statement("DELETE FROM e_signs WHERE status = 'submitted'");

        // Step 3: Ubah ke VARCHAR saja agar lebih fleksible ke depannya
        // Tidak perlu ENUM lagi, pakai VARCHAR agar mudah tambah status baru
    }

    public function down(): void
    {
        // Kembalikan ke status lama
        DB::statement("UPDATE e_signs SET status = 'waiting_employee' WHERE status = 'sign_1'");
        DB::statement("UPDATE e_signs SET status = 'waiting_employee' WHERE status = 'sign_2'");
        DB::statement("UPDATE e_signs SET status = 'waiting_employee' WHERE status = 'sign_3'");
        DB::statement("UPDATE e_signs SET status = 'approved_employee' WHERE status = 'completed'");

        DB::statement("ALTER TABLE e_signs MODIFY COLUMN status ENUM(
            'draft', 'submitted', 'pending', 'approved', 'rejected',
            'completed',
            'waiting_employee',
            'approved_employee', 'rejected_employee'
        ) NOT NULL DEFAULT 'draft' COMMENT 'Status E-Sign'");
    }
};
