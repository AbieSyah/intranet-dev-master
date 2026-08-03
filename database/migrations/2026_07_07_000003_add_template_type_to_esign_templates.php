<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('esign_templates', function (Blueprint $table) {
            $table->string('template_type', 20)->default('editor')
                ->after('content')
                ->comment('Jenis template: editor, docx, pdf, html');
        });
    }

    public function down(): void
    {
        Schema::table('esign_templates', function (Blueprint $table) {
            $table->dropColumn('template_type');
        });
    }
};
