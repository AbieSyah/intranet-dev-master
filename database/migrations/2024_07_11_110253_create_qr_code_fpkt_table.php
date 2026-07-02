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
        Schema::create('qr_code_fpkt', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('id_fkt');
            $table->text('qr');
            $table->dateTime('date_approval', precision: 0);
            $table->integer('type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_code_fpkt');
    }
};
