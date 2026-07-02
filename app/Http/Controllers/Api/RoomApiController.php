<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;

class RoomApiController extends Controller
{
    public function index()
    {
        $rooms = Room::all();

        return response()->json([
            'success' => true,
            'data' => $rooms
        ]);
    }

}
