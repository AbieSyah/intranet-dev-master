<?php

namespace App\Http\Controllers\ESign;

use App\Http\Controllers\Controller;
use App\Services\ESignDummyData;
use Illuminate\Http\Request;

class ESignController extends Controller
{
    public function dashboard()
    {
        $counts = ESignDummyData::getCounts();
        return view('pages.e-sign.dashboard', compact('counts'));
    }

    public function daftarSurat(Request $request)
    {
        $status = $request->query('status');
        $documents = $status
            ? ESignDummyData::getByStatus($status)
            : ESignDummyData::getDocuments();
        $counts = ESignDummyData::getCounts();
        $currentStatus = $status;
        return view('pages.e-sign.daftar-surat', compact('documents', 'counts', 'currentStatus'));
    }

    public function jenisSurat()
    {
        $letterTypes = ESignDummyData::getLetterTypes();
        $typeCounts = ESignDummyData::getTypeCounts();
        return view('pages.e-sign.jenis-surat', compact('letterTypes', 'typeCounts'));
    }

    public function template($jenis)
    {
        $letterTypes = ESignDummyData::getLetterTypes();
        $type = ESignDummyData::getLetterType($jenis);

        if (!$type) {
            abort(404);
        }

        $data = [
            'slug' => $type['slug'],
            'title' => $type['name'],
            'short_title' => $type['name'],
            'number' => strtoupper($type['slug']) . '/2026/001',
        ];

        return view('pages.e-sign.template', compact('data'));
    }
}
