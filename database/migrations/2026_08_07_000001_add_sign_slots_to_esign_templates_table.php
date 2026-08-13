<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('esign_templates', function (Blueprint $table) {
            $table->boolean('sign_1')->default(true)->comment('Slot tanda tangan 1 aktif (posisi kanan bawah)');
            $table->boolean('sign_2')->default(false)->comment('Slot tanda tangan 2 aktif (posisi kiri bawah)');
            $table->boolean('sign_3')->default(false)->comment('Slot tanda tangan 3 aktif (posisi tengah bawah)');
        });
    }

    public function down(): void
    {
        Schema::table('esign_templates', function (Blueprint $table) {
            $table->dropColumn(['sign_1', 'sign_2', 'sign_3']);
        });
    }
};
