<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('e_signs', function (Blueprint $table) {
            $table->boolean('is_parallel_sign')->default(false)->after('nomor_sub')
                ->comment('Penanda tangan paralel (Sign 1 & Sign 2 tanda tangan kapan saja)');
        });
    }

    public function down(): void
    {
        Schema::table('e_signs', function (Blueprint $table) {
            $table->dropColumn('is_parallel_sign');
        });
    }
};