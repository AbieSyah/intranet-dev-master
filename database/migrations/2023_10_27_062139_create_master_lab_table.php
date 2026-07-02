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
        Schema::create('master_lab', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('id_vendor');
            $table->string('pemeriksaan', 255);
            $table->text('nilai_rujukan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_lab');
    }
};
