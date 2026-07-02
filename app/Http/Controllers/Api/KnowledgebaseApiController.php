<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KnowledgeBaseApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user()->load(
                'employee.department',
                'employee.position'
            );

            $knowledgeBases = KnowledgeBase::with([
                    'author',
                    'media',
                    'employees'
                ])
                ->where('status', KnowledgeBase::STATUS_PUBLISHED)
                ->canView($user)
                ->latest()
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $knowledgeBases
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data knowledge base',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}