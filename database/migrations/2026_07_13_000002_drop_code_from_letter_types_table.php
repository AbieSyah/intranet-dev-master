<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('letter_types', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });
    }

    public function down(): void
    {
        Schema::table('letter_types', function (Blueprint $table) {
            $table->string('code', 20)->unique()->after('name')->comment('Kode unik jenis surat (A-Z, 0-9, -)');
        });
    }
};
