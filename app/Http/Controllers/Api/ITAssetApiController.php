<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ITAsset;

class ITAssetApiController extends Controller
{
    public function myAssets(Request $request)
    {
        $user = $request->user();

        $assets = ITAsset::with([
            'assetType',
            'employee.area',
            'employee'
        ])
        ->where('employee_id', $user->employee->id)
        ->latest()
        ->get();

        return response()->json([
            'success' => true,
            'data' => $assets
        ]);
    }
}
