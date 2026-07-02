<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medical;
use Illuminate\Http\Request;

class MedicalApiController extends Controller
{
    public function index(Request $request)
    {
        // user login dari token
        $user = $request->user();

        // ambil employee id
        $employeeId = $user->employee_id;

        $medical = Medical::with(['employee', 'medicalvendor'])
            ->where('id_employees', $employeeId)
            ->latest()
            ->get()
            ->map(function ($item) {
                $item->file_url = $item->lampiran_mcu
                    ? url('/api/medical/' . $item->id . '/file')
                    : null;

                return $item;
            });

        return response()->json([
            'success' => true,
            'data' => $medical
        ]);
    }

    public function showFile(Request $request, $id)
    {
        $user = $request->user();

        $medical = Medical::where('id', $id)
            ->where('id_employees', $user->employee_id)
            ->firstOrFail();

        if (!$medical->lampiran_mcu) {
            return response()->json([
                'message' => 'File tidak ditemukan'
            ], 404);
        }

        $path = storage_path('app/public/mcu/' . $medical->lampiran_mcu);

        if (!file_exists($path)) {
            return response()->json([
                'message' => 'File tidak ada di server'
            ], 404);
        }

        return response()->file($path);
    }
}
