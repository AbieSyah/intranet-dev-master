<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsApiController extends Controller
{
    public function index()
    {
        $news = News::where('status', 'release')
            ->latest()
            ->get()
            ->map(function ($item) {

                $item->tumbnail_url = $item->tumbnail
                    ? url('/api/news/' . $item->id . '/thumbnail')
                    : null;

                $item->gambar_url = $item->gambar
                    ? url('/api/news/' . $item->id . '/gambar')
                    : null;

                $item->video_url = $item->video
                    ? url('/api/news/' . $item->id . '/video')
                    : null;

                return $item;
            });

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    public function showThumbnail($id)
    {
        $news = News::where('status', 'release')->findOrFail($id);

        if (!$news->tumbnail) {
            return response()->json([
                'message' => 'Thumbnail tidak ditemukan'
            ], 404);
        }

        $path = storage_path('app/public/tumbnail/' . $news->tumbnail);

        if (!file_exists($path)) {
            return response()->json([
                'message' => 'File tidak ada di server'
            ], 404);
        }

        return response()->file($path);
    }

    public function showGambar($id)
    {
        $news = News::where('status', 'release')->findOrFail($id);

        if (!$news->gambar) {
            return response()->json([
                'message' => 'Gambar tidak ditemukan'
            ], 404);
        }

        $path = storage_path('app/public/konten/' . $news->gambar);

        if (!file_exists($path)) {
            return response()->json([
                'message' => 'File tidak ada di server'
            ], 404);
        }

        return response()->file($path);
    }

    public function showLampiran($id)
    {

        $news = News::where('status', 'release')->findOrFail($id);

        if (!$news->lampiran) {
            return response()->json([
                'message' => 'File tidak ditemukan'
            ], 404);
        }

        $path = storage_path('app/public/lampiran_konten/' . $news->lampiran);

        if (!file_exists($path)) {
            return response()->json([
                'message' => 'File tidak ada di server'
            ], 404);
        }

        return response()->file($path);
    }

    // public function showVideo($id)
    // {
    //     $news = News::where('status', 'release')->findOrFail($id);

    //     if (!$news->link_video) {
    //         return response()->json([
    //             'message' => 'Link video tidak ditemukan'
    //         ], 404);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'link_video' => $news->link_video
    //     ]);
    // }
}
