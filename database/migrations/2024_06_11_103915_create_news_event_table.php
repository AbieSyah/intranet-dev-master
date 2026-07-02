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
        Schema::create('news_event', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('judul', 255);
            $table->text('detail');
            $table->text('tumbnail');
            $table->text('gambar');
            $table->text('link_video');
            $table->text('lampiran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_event');
    }
};
