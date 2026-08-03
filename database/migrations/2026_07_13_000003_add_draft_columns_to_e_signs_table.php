<?php

use App\Models\ESign;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan kolom yang diperlukan untuk fitur Draft Surat:
     * - letter_type_id       : relasi langsung ke letter_types
     * - template_id          : template yang digunakan saat draft dibuat
     * - title                : judul surat (input user)
     * - content              : snapshot HTML isi surat (dari template)
     * - pdf_path             : path file PDF hasil generate
     * - signed_pdf_path      : path file PDF yang sudah ditandatangani
     * - created_by           : user pembuat draft
     *
     * Juga mengubah nomor_surat menjadi nullable dan menambah status.
     */
    public function up(): void
    {
        Schema::table('e_signs', function (Blueprint $table) {
            // Relasi ke letter_types
            $table->unsignedBigInteger('letter_type_id')
                ->nullable()
                ->after('employee_id')
                ->comment('ID jenis surat (FK ke letter_types)');

            // Relasi ke template yang digunakan saat pembuatan draft
            $table->unsignedBigInteger('template_id')
                ->nullable()
                ->after('letter_type_id')
                ->comment('ID template yang digunakan saat draft dibuat');

            // Judul surat (input manual user)
            $table->string('title', 255)
                ->nullable()
                ->after('document_name')
                ->comment('Judul surat');

            // Snapshot HTML isi surat — copy dari template saat draft dibuat
            $table->longText('content')
                ->nullable()
                ->after('title')
                ->comment('Isi surat (HTML snapshot dari template)');

            // Path untuk PDF dan Signed PDF
            $table->string('pdf_path')
                ->nullable()
                ->after('file_size')
                ->comment('Path file PDF hasil generate');

            $table->string('signed_pdf_path')
                ->nullable()
                ->after('pdf_path')
                ->comment('Path file PDF yang sudah ditandatangani');

            // Pembuat draft (user yang login)
            $table->unsignedBigInteger('created_by')
                ->nullable()
                ->after('signed_pdf_path')
                ->comment('User pembuat draft');
        });

        // Ubah nomor_surat menjadi nullable — draft belum punya nomor
        DB::statement("ALTER TABLE e_signs MODIFY COLUMN nomor_surat VARCHAR(50) NULL COMMENT 'Nomor surat (nullable untuk draft)'");

        // Tambah status baru: submitted, completed
        DB::statement("ALTER TABLE e_signs MODIFY COLUMN status ENUM(
            'draft', 'submitted', 'pending', 'approved', 'rejected',
            'completed',
            'waiting_employee',
            'approved_employee', 'rejected_employee'
        ) NOT NULL DEFAULT 'draft' COMMENT 'Status E-Sign'");

        // Foreign keys
        Schema::table('e_signs', function (Blueprint $table) {
            $table->foreign('letter_type_id')
                ->references('id')
                ->on('letter_types')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('template_id')
                ->references('id')
                ->on('esign_templates')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->index('letter_type_id', 'idx_e_signs_letter_type_id');
            $table->index('template_id', 'idx_e_signs_template_id');
            $table->index('created_by', 'idx_e_signs_created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('e_signs', function (Blueprint $table) {
            $table->dropForeign(['letter_type_id']);
            $table->dropForeign(['template_id']);
            $table->dropForeign(['created_by']);

            $table->dropIndex('idx_e_signs_letter_type_id');
            $table->dropIndex('idx_e_signs_template_id');
            $table->dropIndex('idx_e_signs_created_by');

            $table->dropColumn([
                'letter_type_id',
                'template_id',
                'title',
                'content',
                'pdf_path',
                'signed_pdf_path',
                'created_by',
            ]);
        });

        // Kembalikan nomor_surat ke NOT NULL
        DB::statement("ALTER TABLE e_signs MODIFY COLUMN nomor_surat VARCHAR(50) NOT NULL COMMENT 'Nomor surat otomatis, format: PREFIX/TAHUN/NOMOR_URUT'");

        // Kembalikan status ke sebelumnya
        DB::statement("ALTER TABLE e_signs MODIFY COLUMN status ENUM(
            'draft', 'pending', 'approved', 'rejected',
            'waiting_employee',
            'approved_employee', 'rejected_employee'
        ) NOT NULL DEFAULT 'draft' COMMENT 'Status E-Sign'");
    }
};
