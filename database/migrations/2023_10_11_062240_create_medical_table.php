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
        Schema::create('medical', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('id_employees');
            $table->string('no_lab', 100);
            $table->text('lab');
            $table->text('foto_thorax');
            $table->text('audiometri');
            $table->text('fisik_dokter');
            $table->text('kesimpulan');
            $table->text('saran');
            $table->text('skor_framigham');
            $table->text('kriteria_sehat');
            $table->text('hm_hemoglobin');
            $table->text('hm_eritrosit');
            $table->text('hm_hematokrit');
            $table->text('hm_mcv');
            $table->text('hm_mch');
            $table->text('hm_mchc');
            $table->text('hm_rdw');
            $table->text('hm_leukosit');
            $table->text('hm_eos');
            $table->text('hm_baso');
            $table->text('hm_neutro');
            $table->text('hm_limfo');
            $table->text('hm_mono');
            $table->text('hm_eos_absolut');
            $table->text('hm_baso_absolut');
            $table->text('hm_neutro_absolut');
            $table->text('hm_limfo_absolut');
            $table->text('hm_mono_absolut');
            $table->text('hm_trombosit');
            $table->text('hm_led');
            $table->text('u_warna');
            $table->text('u_kejernihan');
            $table->text('u_berat_jenis');
            $table->text('u_ph');
            $table->text('u_protein_albumin');
            $table->text('u_glukosa');
            $table->text('u_keton');
            $table->text('u_bilirubin');
            $table->text('u_urobilinogen');
            $table->text('u_nitrit');
            $table->text('u_leukosit_esterase');
            $table->text('u_darah_haem');
            $table->text('u_eri');
            $table->text('u_leuko');
            $table->text('u_epithel');
            $table->text('u_silinder');
            $table->text('u_kristal');
            $table->text('u_lain');
            $table->text('fh_sgot');
            $table->text('fh_sgpt');
            $table->text('fl_kolesterol_total');
            $table->text('fl_hdl_kolesterol');
            $table->text('fl_ldl_kolesterol');
            $table->text('fl_trigliserida');
            $table->text('gd_glukosa_puasa');
            $table->text('gd_jpp');
            $table->text('fg_bun');
            $table->text('fg_ureum');
            $table->text('fg_kreatinin');
            $table->text('fg_egfr');
            $table->text('asam_urat');
            $table->text('hbsag');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical');
    }
};
