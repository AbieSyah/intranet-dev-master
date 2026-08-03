<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom layout halaman untuk template surat.
     * Memungkinkan admin mengatur margin dan ukuran kertas
     * seperti di Microsoft Word.
     */
    public function up(): void
    {
        Schema::table('esign_templates', function (Blueprint $table) {
            $table->unsignedInteger('page_margin_top')
                ->default(25)
                ->after('file_original_name')
                ->comment('Margin atas (mm)');

            $table->unsignedInteger('page_margin_bottom')
                ->default(25)
                ->after('page_margin_top')
                ->comment('Margin bawah (mm)');

            $table->unsignedInteger('page_margin_left')
                ->default(25)
                ->after('page_margin_bottom')
                ->comment('Margin kiri (mm)');

            $table->unsignedInteger('page_margin_right')
                ->default(25)
                ->after('page_margin_left')
                ->comment('Margin kanan (mm)');

            $table->string('page_size', 20)
                ->default('A4')
                ->after('page_margin_right')
                ->comment('Ukuran kertas: A4, Letter, Legal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('esign_templates', function (Blueprint $table) {
            $table->dropColumn([
                'page_margin_top',
                'page_margin_bottom',
                'page_margin_left',
                'page_margin_right',
                'page_size',
            ]);
        });
    }
};
