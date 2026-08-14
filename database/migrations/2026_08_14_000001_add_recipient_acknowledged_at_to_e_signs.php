<?php

use App\Models\ESign;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('e_signs', function (Blueprint $table) {
            $table->timestamp('recipient_acknowledged_at')->nullable()->after('employee3_signed_at')
                ->comment('Waktu penerima (yang tidak menandatangani) mengonfirmasi telah membaca surat');
        });

        // Perbaiki surat pengumuman LAMA yang "nyangkut": status sign_2/sign_3 tapi tidak
        // ada penandatangan pada slot tersebut (karena alur lama hanya cek Sign 1 & Sign 2).
        // Surat seperti ini seharusnya masuk alur konfirmasi penerima.
        DB::table('e_signs')
            ->whereIn('status', [ESign::STATUS_SIGN_2, ESign::STATUS_SIGN_3])
            ->whereNotNull('employee_id')
            ->where(function ($q) {
                $q->whereNull('employee2_signee_id')
                  ->orWhereNull('employee3_signee_id');
            })
            ->update(['status' => 'awaiting_ack']);
    }

    public function down(): void
    {
        Schema::table('e_signs', function (Blueprint $table) {
            $table->dropColumn('recipient_acknowledged_at');
        });
    }
};
