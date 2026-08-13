<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('e_sign_batches', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->nullable()->comment('Nomor batch (1 batch = 1 nomor, contoh PKWT/HRD/07/2026)');
            $table->string('jenis_surat_slug')->nullable();
            $table->unsignedBigInteger('letter_type_id')->nullable();
            $table->unsignedBigInteger('template_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->timestamps();

            $table->foreign('letter_type_id')->references('id')->on('letter_types')->onDelete('set null');
            $table->foreign('template_id')->references('id')->on('esign_templates')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('e_sign_batches');
    }
};
