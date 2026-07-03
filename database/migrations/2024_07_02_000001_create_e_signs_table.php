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
        Schema::create('e_signs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('document_name', 255)->comment('Nama dokumen');
            $table->enum('document_type', ['contract', 'approval', 'agreement', 'other'])->comment('Tipe dokumen');
            $table->text('description')->nullable()->comment('Deskripsi dokumen');
            $table->string('document_path')->comment('Path file dokumen');
            $table->string('file_name', 255)->comment('Nama file');
            $table->integer('file_size')->comment('Ukuran file dalam bytes');
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])
                ->default('draft')
                ->comment('Status E-Sign');
            $table->timestamp('upload_date')->nullable()->comment('Tanggal upload');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('employee_id');
            $table->index('status');
            $table->index('document_type');

            // Foreign keys
            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_signs');
    }
};
