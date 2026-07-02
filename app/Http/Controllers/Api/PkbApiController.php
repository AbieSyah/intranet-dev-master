<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pkb;
use Illuminate\Http\Request;

class PkbApiController extends Controller
{
    public function index()
    {
        $pkb = Pkb::latest()->get()->map(function ($item) {
            $item->file_url = $item->file_pkb
                ? url('/api/pkb/' . $item->id . '/file')
                : null;

            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $pkb
        ]);
    }

    public function showFile($id)
    {
        $pkb = Pkb::findOrFail($id);

        if (!$pkb->file_pkb) {
            return response()->json([
                'message' => 'File tidak ditemukan'
            ], 404);
        }

        $path = storage_path('app/public/pkb/' . $pkb->file_pkb);

        if (!file_exists($path)) {
            return response()->json([
                'message' => 'File tidak ada di server'
            ], 404);
        }

        return response()->file($path);
    }
}
