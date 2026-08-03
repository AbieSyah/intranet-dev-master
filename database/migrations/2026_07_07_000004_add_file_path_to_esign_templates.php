<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('esign_templates', function (Blueprint $table) {
            // Add letter_type_id as FK (nullable for existing records)
            $table->unsignedBigInteger('letter_type_id')->nullable()->after('id');
            $table->foreign('letter_type_id')
                ->references('id')->on('letter_types')
                ->onDelete('cascade');

            // Add file columns
            $table->string('file_path', 255)->nullable()->after('content')
                ->comment('Path file DOCX/PDF di storage');
            $table->string('file_original_name', 255)->nullable()->after('file_path')
                ->comment('Nama file asli saat upload');
        });
    }

    public function down(): void
    {
        Schema::table('esign_templates', function (Blueprint $table) {
            $table->dropForeign(['letter_type_id']);
            $table->dropColumn('letter_type_id');
            $table->dropColumn('file_path');
            $table->dropColumn('file_original_name');
        });
    }
};
