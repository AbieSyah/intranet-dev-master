<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Trainingrecord;
use App\Models\Trainingfkt;
use App\Models\Trainingfpkt;
use App\Models\Qrcodefkt;
use App\Models\Qrcodefpkt;
use PDF;

class QrcodeController extends Controller
{
    //start qrcode presiden
    public function training_laporan_qrcode_ttd($id,$ttd1){
        $record = Trainingrecord::where('id', $id)->where('ttd_presiden', $ttd1)->first();
        $cek_ttd = 'ttd1';
        return view('pages.profile.training.laporan.qrcode', compact('record','cek_ttd'));
    }
    //end qrcode presiden
    //start qrcode direktur
    public function training_laporan_qrcode_ttd2($id,$ttd2){
        $record = Trainingrecord::where('id', $id)->where('ttd_direktur', $ttd2)->first();
        $cek_ttd = 'ttd2';
        return view('pages.profile.training.laporan.qrcode', compact('record','cek_ttd'));
    }
    //end qrcode direktur
    //start qrcode general manager
    public function training_laporan_qrcode_ttd3($id,$ttd3){
        $record = Trainingrecord::where('id', $id)->where('ttd_general_manager', $ttd3)->first();
        $cek_ttd = 'ttd3';
        return view('pages.profile.training.laporan.qrcode', compact('record','cek_ttd'));
    }
    //end qrcode general manager
    //start qrcode manager
    public function training_laporan_qrcode_ttd4($id,$ttd4){
        $record = Trainingrecord::where('id', $id)->where('ttd_manager', $ttd4)->first();
        $cek_ttd = 'ttd4';
        return view('pages.profile.training.laporan.qrcode', compact('record','cek_ttd'));
    }
    //end qrcode manager
    //start qrcode atasan
    public function training_laporan_qrcode_ttd5($id,$ttd5){
        $record = Trainingrecord::where('id', $id)->where('ttd_atasan', $ttd5)->first();
        $cek_ttd = 'ttd5';
        return view('pages.profile.training.laporan.qrcode', compact('record','cek_ttd'));
    }
    //end qrcode atasan
    //start qrcode hrd & ga general manager
    public function training_laporan_qrcode_ttd6($id,$ttd6){
        $record = Trainingrecord::where('id', $id)->where('ttd_hrd_ga_gm', $ttd6)->first();
        $cek_ttd = 'ttd6';
        return view('pages.profile.training.laporan.qrcode', compact('record','cek_ttd'));
    }
    //end qrcode hrd & ga general manager
    //start qrcode pic
    public function training_laporan_qrcode_ttd7($id,$ttd7){
        $record = Trainingrecord::where('id', $id)->where('ttd_pic', $ttd7)->first();
        $cek_ttd = 'ttd7';
        return view('pages.profile.training.laporan.qrcode', compact('record','cek_ttd'));
    }
    //end qrcode pic

    //FKP
    public function training_fkp_pdf($id){
        $fkt = Trainingfkt::where('kode', decrypt($id))->first();
        //get qrcode
        $all_qrcode = Qrcodefkt::where('kode_fkt', $fkt->kode)->get();

        //pemohon ttd
        $pemohon = $fkt->pemohon->fullname;
	    $cp_pemohon = Str::lower($pemohon);
        $pemohon_ttd = ucwords($cp_pemohon);
        
        $pos_pemohon = $fkt->pemohon->position->nama ?? '-';
	    $cp_pos_pemohon = Str::lower($pos_pemohon);
        $pos_pemohon_ttd = ucwords($cp_pos_pemohon);

        $qr_1 =  $all_qrcode->whereStrict('type', 1)->first();
        if(!empty($qr_1)){
            $pemohon_qr = $qr_1->qr;
            $pemohon_kode_qr = str_replace("/","-",$qr_1->kode_fkt);

            $link_qr_pemohon = route('public.training.qrcode.fkp.pemohon', ['code' => $pemohon_qr, 'id' => $pemohon_kode_qr]);
        }else{
            $pemohon_qr = null;
            $pemohon_kode_qr = null;

            $link_qr_pemohon = '';
        }
        
        //checker ttd
        $checker = $fkt->checker->fullname;
	    $cp_checker = Str::lower($checker);
        $checker_ttd = ucwords($cp_checker);

        $pos_checker = $fkt->checker->position->nama ?? '-';
	    $cp_pos_checker = Str::lower($pos_checker);
        $pos_checker_ttd = ucwords($cp_pos_checker);

        $qr_2 =  $all_qrcode->whereStrict('type', 5)->first();
        if(!empty($qr_2)){
            $checker_qr = $qr_2->qr;
            $checker_kode_qr = str_replace("/","-",$qr_2->kode_fkt);

            $link_qr_checker = route('public.training.qrcode.fkp.checker', ['code' => $checker_qr, 'id' => $checker_kode_qr]);
        }else{
            $checker_qr = null;
            $checker_kode_qr = null;

            $link_qr_checker = '';
        }

        //hrd verified ttd
        $verified = $fkt->verified->fullname ?? '-';
	    $cp_verified = Str::lower($verified);
        $verified_ttd = ucwords($cp_verified);

        $pos_verified = $fkt->verified->position->nama ?? '-';
	    $cp_pos_verified = Str::lower($pos_verified);
        $pos_verified_ttd = ucwords($cp_pos_verified);

        $qr_3 =  $all_qrcode->whereStrict('type', 3)->first();
        if(!empty($qr_3)){
            $verified_qr = $qr_3->qr;
            $verified_kode_qr = str_replace("/","-",$qr_3->kode_fkt);

            $link_qr_verified = route('public.training.qrcode.fkp.verified', ['code' => $verified_qr, 'id' => $verified_kode_qr]);
        }else{
            $verified_qr = null;
            $verified_kode_qr = null;

            $link_qr_verified = '';
        }

        $arr_fkt = Trainingfkt::where('kode', decrypt($id))->get();
        $data = [
            'title' => 'FORMULIR RENCANA PELATIHAN TAHUNAN',
            'fkt' => $fkt,
            'arr_fkt' => $arr_fkt,
            'pemohon_ttd' => $pemohon_ttd,
            'pos_pemohon_ttd' => $pos_pemohon_ttd,
            'checker_ttd' => $checker_ttd,
            'pos_checker_ttd' => $pos_checker_ttd,
            'verified_ttd' => $verified_ttd,
            'pos_verified_ttd' => $pos_verified_ttd,
            'link_qr_pemohon' => $link_qr_pemohon,
            'link_qr_checker' => $link_qr_checker,
            'link_qr_verified' => $link_qr_verified
        ];
        $pdf = PDF::loadView('pages.profile.fkt', $data)->setPaper('a4', 'landscape');
        $pdf->set_option("isPhpEnabled", true);
        return $pdf->stream('FORMULIR RENCANA PELATIHAN TAHUNAN - '.$fkt->pemohon->fullname.'.pdf');
    }
    //QRCODE FKP
    public function qr_code_pemohon_fkp($code,$id){
        $kode_fkt = str_replace("-","/",$id);
        $query = Qrcodefkt::where('kode_fkt', $kode_fkt)->where('qr', $code)->where('type', 1)->first();
        $usulan = 'Program Pelatihan Tahunan';
        return view('pages.profile.codeqr-pemohon', compact('query','usulan'));
    }
    public function qr_code_checker_fkp($code,$id){
        $kode_fkt = str_replace("-","/",$id);
        $query = Qrcodefkt::where('kode_fkt', $kode_fkt)->where('qr', $code)->where('type', 5)->first();
        $usulan = 'Program Pelatihan Tahunan';
        return view('pages.profile.codeqr-checker', compact('query','usulan'));
    }
    public function qr_code_verified_fkp($code,$id){
        $kode_fkt = str_replace("-","/",$id);
        $query = Qrcodefkt::where('kode_fkt', $kode_fkt)->where('qr', $code)->where('type', 3)->first();
        $usulan = 'Program Pelatihan Tahunan';
        return view('pages.profile.codeqr-verified', compact('query','usulan'));
    }

    //FPKP
    public function training_fpkp_pdf($id){
        $html = '';
        $fpkt = Trainingfpkt::where('id', decrypt($id))->first();
        $arr_tujuan = $fpkt->tujuan;
        $arr_kompetensi = $fpkt->kompetensi;
        $arr_skill = json_decode($fpkt->skill, true);
        $arr_peserta = json_decode($fpkt->level_peserta, true);
        $arr_atasan = json_decode($fpkt->level_atasan, true);
        $arr_rata = json_decode($fpkt->level_rata, true);
        $arr_kebutuhan = json_decode($fpkt->level_kebutuhan, true);
        if(isset($arr_skill)){
            $jumlah = count($arr_skill);
            for($i = 0; $i < count($arr_skill); $i++){
                $arr_fpkt['tujuan'] = $arr_tujuan;
                $arr_fpkt['kompetensi'] = $arr_kompetensi;
                $arr_fpkt['skill'] = $arr_skill[$i];
                if(!empty($arr_peserta)){
                    $arr_fpkt['level_peserta'] = $arr_peserta[$i];
                }else{
                    $arr_fpkt['level_peserta'] = '';
                }
                if(!empty($arr_atasan)){
                    $arr_fpkt['level_atasan'] = $arr_atasan[$i];
                }else{
                    $arr_fpkt['level_atasan'] = '';
                }
                if(!empty($arr_rata)){
                    $arr_fpkt['level_rata'] = $arr_rata[$i];
                }else{
                    $arr_fpkt['level_rata'] = '';
                }
                if(!empty($arr_kebutuhan)){
                    $arr_fpkt['level_kebutuhan'] = $arr_kebutuhan[$i];
                }else{
                    $arr_fpkt['level_kebutuhan'] = '';
                }
                $arr_data[] = $arr_fpkt;
            }
        }else{
            $jumlah = 0;
            $arr_fpkt['tujuan'] = '-';
            $arr_fpkt['kompetensi'] = '-';
            $arr_fpkt['skill'] = '-';
            $arr_fpkt['level_peserta'] = '-';
            $arr_fpkt['level_atasan'] = '-';
            $arr_fpkt['level_rata'] = '-';
            $arr_fpkt['level_kebutuhan'] = '-';
            $arr_data[] = $arr_fpkt;
        }
        //ttd peserta
        $qr_1 = Qrcodefpkt::where('id_fpkt', $fpkt->id)->where('type', 1)->first();
        if(!empty($qr_1)){
            $peserta_qr = $qr_1->qr;
            $peserta_fpkt_id = $qr_1->id_fpkt;
            $link_qr_peserta = route('public.training.qrcode.fpkp.peserta', ['code' => $peserta_qr, 'id' => $peserta_fpkt_id]);
        }else{
            $peserta_qr = null;
            $peserta_fpkt_id = null;
            $link_qr_peserta = '';
        }
        //ttd atasan
        $qr_2 = Qrcodefpkt::where('id_fpkt', $fpkt->id)->where('type', 2)->first();
        if(!empty($qr_2)){
            $atasan_qr = $qr_2->qr;
            $atasan_fpkt_id = $qr_2->id_fpkt;
            $link_qr_atasan = route('public.training.qrcode.fpkp.atasan', ['code' => $atasan_qr, 'id' => $atasan_fpkt_id]);
        }else{
            $atasan_qr = null;
            $atasan_fpkt_id = null;
            $link_qr_atasan = '';
        }
        //ttd dept head
        $qr_3 = Qrcodefpkt::where('id_fpkt', $fpkt->id)->where('type', 3)->first();
        if(!empty($qr_3)){
            $dept_qr = $qr_3->qr;
            $dept_fpkt_id = $qr_3->id_fpkt;
            $link_qr_dept = route('public.training.qrcode.fpkp.dept-head', ['code' => $dept_qr, 'id' => $dept_fpkt_id]);
        }else{
            $dept_qr = null;
            $dept_fpkt_id = null;
            $link_qr_dept = '';
        }
        //ttd mr.mizukami
        $qr_4 = Qrcodefpkt::where('id_fpkt', $fpkt->id)->where('type', 4)->first();
        if(!empty($qr_4)){
            $bod1_qr = $qr_4->qr;
            $bod1_fpkt_id = $qr_4->id_fpkt;
            $link_qr_bod1 = route('public.training.qrcode.fpkp.hrd', ['code' => $bod1_qr, 'id' => $bod1_fpkt_id]);
        }else{
            $bod1_qr = null;
            $bod1_fpkt_id = null;
            $link_qr_bod1 = '';
        }
        //ttd mr.sakurai
        $qr_5 = Qrcodefpkt::where('id_fpkt', $fpkt->id)->where('type', 5)->first();
        if(!empty($qr_5)){
            $bod2_qr = $qr_5->qr;
            $bod2_fpkt_id = $qr_5->id_fpkt;
            $link_qr_bod2 = route('public.training.qrcode.fpkp.hrd', ['code' => $bod2_qr, 'id' => $bod2_fpkt_id]);
        }else{
            $bod2_qr = null;
            $bod2_fpkt_id = null;
            $link_qr_bod2 = '';
        }
        //ttd hrd
        $qr_6 = Qrcodefpkt::where('id_fpkt', $fpkt->id)->where('type', 6)->first();
        if(!empty($qr_6)){
            $hrd_qr = $qr_6->qr;
            $hrd_fpkt_id = $qr_6->id_fpkt;
            $link_qr_hrd = route('public.training.qrcode.fpkp.hrd', ['code' => $hrd_qr, 'id' => $hrd_fpkt_id]);
        }else{
            $hrd_qr = null;
            $hrd_fpkt_id = null;
            $link_qr_hrd = '';
        }
        //skor kebutuhan training
        if(!empty($arr_rata)){
            $sum_rata = array_sum($arr_rata);
        }else{
            $sum_rata = 0;
        }
        
        if($sum_rata > 0 && $jumlah > 0){
            $skor = floor($sum_rata/$jumlah);
        }else{
            $skor = 0;
        }

        $data = [
            'title' => 'Formulir Pelaksanaan Pelatihan',
            'fpkt' => $fpkt,
            'arr_fpkt' => $arr_data,
            'jumlah' => $jumlah,
            'skor' => $skor,
            'link_qr_peserta' => $link_qr_peserta,
            'link_qr_atasan' => $link_qr_atasan,
            'link_qr_dept' => $link_qr_dept,
            'link_qr_hrd' => $link_qr_hrd,
            'link_qr_bod1' => $link_qr_bod1,
            'link_qr_bod2' => $link_qr_bod2
        ];
        $view = view('pages.profile.fpkt')->with(compact('data'));
        $html .= $view->render();

        $pdf = PDF::set_option("isPhpEnabled", false);
        $pdf->loadHTML($html);
        $pdf->setPaper('a4', 'landscape');
        return $pdf->stream('FORMULIR PELAKSANAAN PELATIHAN.pdf');
    }
    //QRCODE FPKP
    public function qr_code_peserta_fpkp($code,$id){
        $query = Qrcodefpkt::where('id_fpkt', $id)->where('qr', $code)
            ->where('type', 1)->first();
        return view('pages.profile.codeqr-fpkt', compact('query'));
    }
    public function qr_code_atasan_fpkp($code,$id){
        $query = Qrcodefpkt::where('id_fpkt', $id)->where('qr', $code)
            ->where('type', 2)->first();
        return view('pages.profile.codeqr-fpkt', compact('query'));
    }
    public function qr_code_dept_head_fpkp($code,$id){
        $query = Qrcodefpkt::where('id_fpkt', $id)->where('qr', $code)
            ->where('type', 3)->first();
        return view('pages.profile.codeqr-fpkt', compact('query'));
    }
    public function qr_code_bod1_fpkp($code,$id){
        $query = Qrcodefpkt::where('id_fpkt', $id)->where('qr', $code)
            ->where('type', 4)->first();
        return view('pages.profile.codeqr-fpkt', compact('query'));
    }
    public function qr_code_bod2_fpkp($code,$id){
        $query = Qrcodefpkt::where('id_fpkt', $id)->where('qr', $code)
            ->where('type', 5)->first();
        return view('pages.profile.codeqr-fpkt', compact('query'));
    }
    public function qr_code_hrd_fpkp($code,$id){
        $query = Qrcodefpkt::where('id_fpkt', $id)->where('qr', $code)
            ->where('type', 6)->first();
        return view('pages.profile.codeqr-fpkt', compact('query'));
    }    
}
