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
        Schema::create('internal_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama', 255);
            $table->date('tgl_berlaku');
            $table->text('isi');
            $table->string('status', 255);
            $table->date('tgl_revisi');
            $table->string('file', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_rules');
    }
};
