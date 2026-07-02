<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance\AttendanceCalendar;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AttendanceCalendarController extends Controller
{
    public function index(){
        return view('pages.attendance.master.Calendar.index');
    }

    public function data(Request $request)
{
    $query = AttendanceCalendar::where('is_active', true)
        ->orderBy('date');

    if ($request->exists('is_hq')) {
        $query->where('is_hq', (int)$request->is_hq);
    }

    $calendars = $query->get();

    $events = [];

    foreach ($calendars as $cal) {
        $events[] = [
            'id' => $cal->id,
            'title' => $cal->name,
            'start' => \Carbon\Carbon::parse($cal->date)->format('Y-m-d'),
            'allDay' => true,
            'type' => $cal->type,
        ];
    }

    return response()->json($events);
}
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'date' => 'required|date',
            'type' => 'required',
            'is_hq' => 'required'
        ]);
        // 🔥 Ambil semua event di tanggal itu
        $existingEvents = AttendanceCalendar::where('date', $request->date)
            ->where('is_hq', $request->is_hq)
            ->get();
        // ❌ RULE 1: tidak boleh duplicate type
        if ($existingEvents->where('type', $request->type)->count() > 0) {
            return response()->json([
                'status' => false,
                'message' => 'Event dengan type ini sudah ada di tanggal tersebut!'
            ]);
        }
        // ❌ RULE 2: kalau sudah ada national → tidak boleh tambah company
        if (
            $request->type === 'company' &&
            $existingEvents->where('type', 'national')->count() > 0
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Tidak bisa menambahkan Company Event karena sudah ada National Holiday!'
            ]);
        }
        // ❌ RULE 3: kalau mau tambah national → hapus company (opsional)
        if (
            $request->type === 'national' &&
            $existingEvents->where('type', 'company')->count() > 0
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Hapus Company Event terlebih dahulu sebelum menambahkan National Holiday!'
            ]);
        }
        // ✅ CREATE
        $calendar = AttendanceCalendar::create([
            'name' => $request->name,
            'date' => $request->date,
            'type' => $request->type,
            'is_hq' => $request->is_hq,
            'is_active' => true
        ]);
        $user = Auth::user();
        Log::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'action' => 'insert',
            'description' => "{$user->employee->fullname} create Attendance Calendar ({$calendar->name}/{$calendar->type})"
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Event berhasil ditambahkan!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $calendar = AttendanceCalendar::findOrFail($id);
        $existingEvents = AttendanceCalendar::where('date', $request->date)
            ->where('is_hq', $request->is_hq)
            ->where('id', '!=', $id)
            ->get();
        if ($existingEvents->where('type', $request->type)->count() > 0) {
            return response()->json([
                'message' => 'Event type sudah ada di tanggal ini!'
            ], 422);
        }
        if (
            $request->type === 'company' &&
            $existingEvents->where('type', 'national')->count() > 0
        ) {
            return response()->json([
                'message' => 'Tidak bisa ubah ke Company karena ada National!'
            ], 422);
        }
        $calendar->update([
            'name' => $request->name,
            'date' => $request->date,
            'type' => $request->type,
            'is_hq' => $request->is_hq
        ]);
        $user = Auth::user();
        Log::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'action' => 'Update',
            'description' => "{$user->employee->fullname} Update Attendance Calendar ({$calendar->name}/{$calendar->type})"
        ]);
        return response()->json([
            'message' => 'Event updated successfully'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $calendar = AttendanceCalendar::findOrFail($id);

        $name = $calendar->name;
        $type = $calendar->type;

        $calendar->delete();

        $user = Auth::user();
        Log::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'action' => 'Delete',
            'description' => "{$user->employee->fullname} Delete Attendance Calendar ({$name}/libur-{$type})"
        ]);

        return response()->json([
            'message' => 'Event deleted successfully'
        ]);
    }
    public function syncNational(Request $request){
         $year = date('Y'); // ambil tahun sekarang
    // ambil dari API
    $response = Http::get('https://date.nager.at/api/v3/PublicHolidays/' . $year . '/ID');
    // kalau gagal
    if (!$response->successful()) {
        return back()->with('error', 'Gagal ambil data libur');
    }
    $holidays = $response->json();
    foreach ($holidays as $holiday) {
        // simpan HQ
        \App\Models\Attendance\AttendanceCalendar::updateOrCreate(
            [
                'date' => $holiday['date'],
                'is_hq' => 1
            ],
            [
                'name' => $holiday['localName'],
                'type' => 'national',
                'is_active' => true
            ]
        );
        // simpan HO
        \App\Models\Attendance\AttendanceCalendar::updateOrCreate(
            [
                'date' => $holiday['date'],
                'is_hq' => 0
            ],
            [
                'name' => $holiday['localName'],
                'type' => 'national',
                'is_active' => true
            ]
        );
    }
    return back()->with('success', 'Libur nasional berhasil disync!');
    }
}
