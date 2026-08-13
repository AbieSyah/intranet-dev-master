<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('letter_types', function (Blueprint $table) {
            $table->boolean('multi_enabled')->default(true)->comment('Jenis surat dapat dikirim sebagai multi-surat (banyak penerima)');
        });

        Schema::table('esign_templates', function (Blueprint $table) {
            $table->boolean('sign_1_is_recipient')->default(false)->comment('Slot tanda tangan 1 adalah penerima (berubah per salinan pada multi-surat)');
            $table->boolean('sign_2_is_recipient')->default(true)->comment('Slot tanda tangan 2 adalah penerima (berubah per salinan pada multi-surat)');
            $table->boolean('sign_3_is_recipient')->default(false)->comment('Slot tanda tangan 3 adalah penerima (berubah per salinan pada multi-surat)');
        });
    }

    public function down(): void
    {
        Schema::table('esign_templates', function (Blueprint $table) {
            $table->dropColumn(['sign_1_is_recipient', 'sign_2_is_recipient', 'sign_3_is_recipient']);
        });

        Schema::table('letter_types', function (Blueprint $table) {
            $table->dropColumn('multi_enabled');
        });
    }
};
