<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('biometric_key')->nullable()->comment('Key used for biometric fingerprint authentication');
            $table->string('biometric_device_id')->nullable()->comment('Device ID for biometric binding');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('biometric_key');
            $table->dropColumn('biometric_device_id');
        });
    }
};
