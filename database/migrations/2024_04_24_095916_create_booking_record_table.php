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
        Schema::create('booking_record', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('brief_description', 255);
            $table->text('full_description');
            $table->dateTime('date_start', $precision = 0);
            $table->dateTime('date_end', $precision = 0);
            $table->integer('room_id');
            $table->enum('status', ['tentative', 'confirmed']);
            $table->enum('repeat_type', ['none', 'daily', 'weekly', 'monthly']);
            $table->date('repeat_end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_record');
    }
};
