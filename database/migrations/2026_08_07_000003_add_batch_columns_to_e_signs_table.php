<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('e_signs', function (Blueprint $table) {
            $table->unsignedBigInteger('batch_id')->nullable()->after('template_id')
                ->comment('Batch id untuk multi-surat (grup dari 1 transaksi)');
            $table->unsignedInteger('nomor_sub')->nullable()->after('batch_id')
                ->comment('Urutan sub-index dalam batch (1..N)');

            $table->foreign('batch_id')->references('id')->on('e_sign_batches')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('e_signs', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn(['batch_id', 'nomor_sub']);
        });
    }
};
