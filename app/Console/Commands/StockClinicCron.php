<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Clinic\Trmasuk;
use App\Models\Clinic\Trkeluar;
use App\Models\Clinic\Prestock;
use App\Models\Master\Drug;
use Carbon\Carbon;

class StockClinicCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stockclinic:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //prestock date
        $prestock_date = date('Y-m', strtotime("-2 months"));
        $prestock_month = date('m', strtotime($prestock_date));
        $prestock_year = date('Y', strtotime($prestock_date));
        //prev date
        $prev_date = date('Y-m', strtotime("-1 months"));
        $prev_month = date('m', strtotime($prev_date));
        $prev_year = date('Y', strtotime($prev_date));
        //date now
        $date_now = date('m/d/Y', strtotime($prev_date.'-01'));
        $date = Carbon::createFromFormat('m/d/Y', $date_now)
            ->endOfMonth()
            ->format('Y-m-d');
        //all drug
        $all_drug = Drug::get();
        foreach($all_drug as $drug){
            $prestock = Prestock::where('id_drug', $drug->id)->whereYear('tanggal', $prestock_year)
                ->whereMonth('tanggal', $prestock_month)->sum('jml_drug');
            $jml_in = Trmasuk::where('id_drug', $drug->id)
                ->whereYear('tr_tanggal', $prev_year)
                ->whereMonth('tr_tanggal', $prev_month)
                ->sum('jml_drug');
            $jml_out = Trkeluar::where('id_drug', $drug->id)
                ->whereYear('tr_tanggal', $prev_year)
                ->whereMonth('tr_tanggal', $prev_month)
                ->sum('jml_drug');

            $end_stock = ($prestock+$jml_in)-$jml_out;
            $insert_stock[] = [
                'id_drug' => $drug->id,
                'nama_drug' => $drug->nama,
                'tanggal' => $date,
                'jml_drug' => $end_stock,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }
        //insert prestock
        $post = Prestock::insert($insert_stock);
    }
}
