<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;

class BookingApiController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['room', 'employee'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bookings
        ]);
    }

    // public function store(Request $request)
    // {
    //     DB::beginTransaction();

    //     try {
    //         $user = $request->user();

    //         if (!$user || !$user->employee_id) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'User not found or has no employee record'
    //             ], 400);
    //         }

    //         $validated = $request->validate([
    //             'brief_description' => 'required|string|max:255',
    //             'full_description' => 'nullable|string',
    //             'start_date' => 'required|date',
    //             'end_date'   => 'required|date|after_or_equal:start_date',
    //             'start_time' => 'required|date_format:H:i',
    //             'end_time' => 'required|date_format:H:i|after:start_time',
    //             'room_id' => 'required|exists:master_room,id',
    //             'tipe' => 'required|string|in:internal,eksternal',
    //             'status' => 'sometimes|string|in:tentative,confirmed',
    //         ]);

    //         $startDate = Carbon::parse($validated['start_date']);
    //         $endDate   = Carbon::parse($validated['end_date']);

    //         // Tentukan apakah ini seri (lebih dari satu hari)
    //         $isSeries = !$startDate->eq($endDate);

    //         // Generate kode hanya untuk seri, gunakan time() seperti di HRD Controller
    //         $kode = $isSeries ? (string) time() : null;

    //         $period = CarbonPeriod::create($startDate, $endDate);
    //         $createdBookings = [];

    //         foreach ($period as $date) {
    //             $date_start = Carbon::parse(
    //                 $date->format('Y-m-d') . ' ' . $validated['start_time']
    //             );
    //             $date_end = Carbon::parse(
    //                 $date->format('Y-m-d') . ' ' . $validated['end_time']
    //             );

    //             // Cek konflik per hari
    //             $conflicts = $this->checkConflicts(
    //                 $validated['room_id'],
    //                 $date_start,
    //                 $date_end
    //             );

    //             if (!empty($conflicts)) {
    //                 DB::rollback();
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'Room conflict detected',
    //                     'conflicts' => $conflicts
    //                 ], 409);
    //             }

    //             $createdBookings[] = Booking::create([
    //                 'brief_description' => $validated['brief_description'],
    //                 'full_description'  => $validated['full_description'],
    //                 'date_start'        => $date_start,
    //                 'date_end'          => $date_end,
    //                 'room_id'           => $validated['room_id'],
    //                 'employee_id'       => $user->employee_id,
    //                 'tipe'              => $validated['tipe'],
    //                 'status'            => $validated['status'] ?? 'tentative',
    //                 'kode'              => $kode, // Diisi hanya untuk seri
    //             ]);
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Booking created successfully',
    //             'total_created' => count($createdBookings),
    //             'kode_seri' => $kode, // Akan null jika bukan seri
    //             'data' => $createdBookings
    //         ], 201);

    //     } catch (\Exception $e) {
    //         DB::rollback();

    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = $request->user();

            if (!$user || !$user->employee_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found or has no employee record'
                ], 400);
            }

            $validated = $request->validate([
                'brief_description' => 'required|string|max:255',
                'full_description'  => 'nullable|string',
                'start_date'        => 'required|date',
                'end_date'          => 'required|date|after_or_equal:start_date',
                'start_time'        => 'required|date_format:H:i',
                'end_time'          => 'required|date_format:H:i|after:start_time',
                'room_id'           => 'required|exists:master_room,id',
                'tipe'              => 'required|string|in:internal,external',
                'status'            => 'sometimes|string|in:tentative,confirmed',
            ]);

            // Gabungkan tanggal dan waktu menjadi satu datetime
            $date_start = Carbon::parse($validated['start_date'] . ' ' . $validated['start_time']);
            $date_end   = Carbon::parse($validated['end_date']   . ' ' . $validated['end_time']);

            // Cek konflik untuk rentang waktu tersebut
            $conflicts = $this->checkConflicts(
                $validated['room_id'],
                $date_start,
                $date_end
            );

            if (!empty($conflicts)) {
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Room conflict detected',
                    'conflicts' => $conflicts
                ], 409);
            }

            // Buat satu booking (kode = null karena bukan seri)
            $booking = Booking::create([
                'brief_description' => $validated['brief_description'],
                'full_description'  => $validated['full_description'],
                'date_start'        => $date_start,
                'date_end'          => $date_end,
                'room_id'           => $validated['room_id'],
                'employee_id'       => $user->employee_id,
                'tipe'              => $validated['tipe'],
                'status'            => $validated['status'] ?? 'tentative',
                'kode'              => time(), // Tidak ada seri
            ]);

            $room = Room::find($validated['room_id']);
            $roomName = $room ? $room->nama : 'Unknown Room';

            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'insert';
            $insert->description = 'Create new booking '.'"'.$validated['brief_description'].'" room "'.$roomName.'"';
            $insert->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Create $request->brief_description has been created",
                'data'    => $booking->load(['room', 'employee'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function checkConflicts($room_id, $date_start, $date_end)
    {
        $start = $date_start instanceof Carbon ? $date_start : Carbon::parse($date_start);
        $end   = $date_end instanceof Carbon ? $date_end : Carbon::parse($date_end);

        $conflicts = Booking::where('room_id', $room_id)
            ->where(function ($query) use ($start, $end) {
                $query->where('date_start', '<', $end)
                    ->where('date_end', '>', $start);
            })
            ->with('room')
            ->get();

        if ($conflicts->isEmpty()) {
            return [];
        }

        return $conflicts->map(function ($booking) {
            $date_start = $booking->date_start instanceof Carbon
                ? $booking->date_start
                : Carbon::parse($booking->date_start);
            $date_end   = $booking->date_end instanceof Carbon
                ? $booking->date_end
                : Carbon::parse($booking->date_end);

            return [
                'id'                 => $booking->id,
                'brief_description'  => $booking->brief_description,
                'date_start'         => $date_start->format('Y-m-d H:i'),
                'date_end'           => $date_end->format('Y-m-d H:i'),
                'room'               => $booking->room->nama ?? 'Unknown',
            ];
        })->toArray();
    }

    public function myBooking(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->employee_id) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak memiliki employee_id'
            ], 400);
        }

        $bookings = Booking::with(['room', 'employee'])
            ->where('employee_id', $user->employee_id)
            ->where(function ($q) {
                $q->whereDate('date_start', today()) // hari ini
                ->orWhere('date_end', '>=', now()); // future
            })
            ->orderBy('date_start', 'asc')
            ->get();

        // dd(now(), $bookings);

        return response()->json([
            'employee_id' => $user->employee_id,
            // 'employee_name' => $user->employee->fullname ?? 'Unknown',
            'success' => true,
            'data' => $bookings
        ]);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $user = $request->user();

            if (!$user || !$user->employee_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found or has no employee record'
                ], 400);
            }

            $booking = Booking::with('room')->find($id);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            $validated = $request->validate([
                'brief_description' => 'required|string|max:255',
                'full_description' => 'nullable|string',
                'date_start' => 'required|date',
                'date_end' => 'required|date|after:date_start',
                'room_id' => 'required|exists:master_room,id',
                'tipe' => 'required|string|in:internal,eksternal',
                'status' => 'sometimes|string|in:tentative,confirmed'
            ]);

            $date_start = Carbon::parse($validated['date_start']);
            $date_end = Carbon::parse($validated['date_end']);

            $conflict = Booking::where('room_id', $validated['room_id'])
                ->where('id', '!=', $id)
                ->where(function ($query) use ($date_start, $date_end) {
                    $query->where('date_start', '<', $date_end)
                        ->where('date_end', '>', $date_start);
                })
                ->exists();

            if ($conflict) {
                $conflictDetails = Booking::where('room_id', $validated['room_id'])
                    ->where('id', '!=', $id)
                    ->where(function ($query) use ($date_start, $date_end) {
                        $query->where('date_start', '<', $date_end)
                            ->where('date_end', '>', $date_start);
                    })
                    ->with('room')
                    ->get();

                return response()->json([
                    'success' => false,
                    'message' => 'Room already booked at this time',
                    'conflicts' => $conflictDetails->map(function($item) {
                        return [
                            'id' => $item->id,
                            'brief_description' => $item->brief_description,
                            'date_start' => Carbon::parse($item->date_start)->format('Y-m-d H:i'),
                            'date_end' => Carbon::parse($item->date_end)->format('Y-m-d H:i'),
                            'room' => $item->room->nama ?? 'Unknown'
                        ];
                    })
                ], 409);
            }

            if ($conflict) {
                $conflictDetails = Booking::where('room_id', $validated['room_id'])
                    ->where('id', '!=', $id)
                    ->where(function($query) use ($date_start, $date_end) {
                        $query->whereBetween('date_start', [$date_start, $date_end])
                            ->orWhereBetween('date_end', [$date_start, $date_end])
                            ->orWhere(function($q) use ($date_start, $date_end) {
                                $q->where('date_start', '<=', $date_start)
                                    ->where('date_end', '>=', $date_end);
                            });
                    })
                    ->with('room')
                    ->get();

                return response()->json([
                    'success' => false,
                    'message' => 'Room already booked at this time',
                    'conflicts' => $conflictDetails->map(function($item) {
                        return [
                            'id' => $item->id,
                            'brief_description' => $item->brief_description,
                            'date_start' => Carbon::parse($item->date_start)->format('Y-m-d H:i'),
                            'date_end' => Carbon::parse($item->date_end)->format('Y-m-d H:i'),
                            'room' => $item->room->nama ?? 'Unknown'
                        ];
                    })
                ], 409);
            }

            $booking->update([
                'brief_description' => $validated['brief_description'],
                'full_description' => $validated['full_description'],
                'date_start' => $date_start,
                'date_end' => $date_end,
                'room_id' => $validated['room_id'],
                'tipe' => $validated['tipe'],
                'status' => $validated['status'] ?? $booking->status
            ]);

            // Log::create([
            //     'user_id' => $user->id,
            //     'ip_address' => $request->ip(),
            //     'action' => 'update',
            //     'description' => 'Modify booking "' . $booking->brief_description .
            //         '" start "' . $date_start->format('Y-m-d H:i') .
            //         '" end "' . $date_end->format('Y-m-d H:i') .
            //         '" room "' . $booking->room->nama . '"'
            // ]);

            $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'update';
                $insert->description = 'Modify booking "' . $booking->brief_description .
                    '" start "' . $date_start->format('Y-m-d H:i') .
                    '" end "' . $date_end->format('Y-m-d H:i') .
                    '" room "' . $booking->room->nama . '"';
                $insert->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking updated successfully',
                'data' => $booking->load(['room', 'employee'])
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // public function updateSeries(Request $request, $kode)
    // {
    //     DB::beginTransaction();

    //     try {
    //         $user = $request->user();

    //         if (!$user || !$user->employee_id) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'User not found or has no employee record'
    //             ], 400);
    //         }

    //         // Cek apakah seri dengan kode tersebut ada
    //         $existingBookings = Booking::where('kode', $kode)->get();
    //         if ($existingBookings->isEmpty()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Series not found'
    //             ], 404);
    //         }

    //         // Validasi input (sama seperti store)
    //         $validated = $request->validate([
    //             'brief_description' => 'required|string|max:255',
    //             'full_description'  => 'nullable|string',
    //             'start_date'        => 'required|date',
    //             'end_date'          => 'required|date|after_or_equal:start_date',
    //             'start_time'        => 'required|date_format:H:i',
    //             'end_time'          => 'required|date_format:H:i|after:start_time',
    //             'room_id'           => 'required|exists:master_room,id',
    //             'tipe'              => 'required|string|in:internal,eksternal',
    //             'status'            => 'sometimes|string|in:tentative,confirmed',
    //         ]);

    //         // Hapus semua booking lama dengan kode ini
    //         Booking::where('kode', $kode)->delete();

    //         $startDate = Carbon::parse($validated['start_date']);
    //         $endDate   = Carbon::parse($validated['end_date']);

    //         $period = CarbonPeriod::create($startDate, $endDate);
    //         $createdBookings = [];

    //         foreach ($period as $date) {
    //             $date_start = Carbon::parse(
    //                 $date->format('Y-m-d') . ' ' . $validated['start_time']
    //             );
    //             $date_end = Carbon::parse(
    //                 $date->format('Y-m-d') . ' ' . $validated['end_time']
    //             );

    //             // Cek konflik per hari
    //             $conflicts = $this->checkConflicts(
    //                 $validated['room_id'],
    //                 $date_start,
    //                 $date_end
    //             );

    //             if (!empty($conflicts)) {
    //                 DB::rollback();
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'Room conflict detected',
    //                     'conflicts' => $conflicts
    //                 ], 409);
    //             }

    //             $createdBookings[] = Booking::create([
    //                 'brief_description' => $validated['brief_description'],
    //                 'full_description'  => $validated['full_description'],
    //                 'date_start'        => $date_start,
    //                 'date_end'          => $date_end,
    //                 'room_id'           => $validated['room_id'],
    //                 'employee_id'       => $user->employee_id,
    //                 'tipe'              => $validated['tipe'],
    //                 'status'            => $validated['status'] ?? 'tentative',
    //                 'kode'              => $kode, // Gunakan kode yang sama
    //             ]);
    //         }

    //         // Catat log aktivitas
    //         // Log::create([
    //         //     'user_id' => $user->id,
    //         //     'ip_address' => $request->ip(),
    //         //     'action' => 'update_series',
    //         //     'description' => "Update series {$kode}: {$validated['brief_description']} (Total: " . count($createdBookings) . " bookings)"
    //         // ]);

    //         $insert = new Log;
    //         $insert->user_id = $user->id;
    //         $insert->ip_address = $request->ip();
    //         $insert->action = 'update_series';
    //         $insert->description = "Update series {$kode}: {$validated['brief_description']} (Total: " . count($createdBookings) . " bookings)";
    //         $insert->save();

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => "Series updated successfully (" . count($createdBookings) . " bookings affected)",
    //             'data' => [
    //                 'kode_series' => $kode,
    //                 'total_updated' => count($createdBookings),
    //                 'bookings' => Booking::where('kode', $kode)->with(['room', 'employee'])->get()
    //             ]
    //         ]);

    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         DB::rollback();
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validation failed',
    //             'errors' => $e->errors()
    //         ], 422);

    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function delete($id, Request $request)
    {
        DB::beginTransaction();

        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 400);
            }

            $booking = Booking::with('room')->find($id);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            $briefDesc = $booking->brief_description;
            $dateStart = Carbon::parse($booking->date_start)->format('Y-m-d H:i');
            $dateEnd = Carbon::parse($booking->date_end)->format('Y-m-d H:i');
            $roomName = $booking->room->nama ?? 'Unknown';

            // Log::create([
            //     'user_id' => $user->id,
            //     'ip_address' => $request->ip(),
            //     'action' => 'delete',
            //     'description' => "Delete booking '{$briefDesc}' start '{$dateStart}' end '{$dateEnd}' room '{$roomName}'"
            // ]);

            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'delete';
            $insert->description = "Delete booking '{$briefDesc}' start '{$dateStart}' end '{$dateEnd}' room '{$roomName}'";
            $insert->save();
            $booking->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Booking '{$briefDesc}' deleted successfully"
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // public function deleteSeries($kode, Request $request)
    // {
    //     DB::beginTransaction();

    //     try {
    //         $user = $request->user();

    //         if (!$user) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'User not found'
    //             ], 400);
    //         }

    //         $bookings = Booking::with('room')->where('kode', $kode)->get();

    //         if ($bookings->isEmpty()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Series not found'
    //             ], 404);
    //         }

    //         $totalInSeries = $bookings->count();

    //         $firstBooking = $bookings->first();
    //         $briefDesc = $firstBooking->brief_description;
    //         $roomName = $firstBooking->room->nama ?? 'Unknown';

    //         $dates = $bookings->map(function($booking) {
    //             return Carbon::parse($booking->date_start)->format('Y-m-d');
    //         })->sort()->values()->toArray();

    //         // Log::create([
    //         //     'user_id' => $user->id,
    //         //     'ip_address' => $request->ip(),
    //         //     'action' => 'delete_series',
    //         //     'description' => "Delete series '{$kode}': {$briefDesc} room '{$roomName}' (Total: {$totalInSeries} bookings, Dates: " . implode(', ', $dates) . ")"
    //         // ]);

    //         $insert = new Log;
    //         $insert->user_id = $user->id;
    //         $insert->ip_address = $request->ip();
    //         $insert->action = 'delete_series';
    //         $insert->description = "Delete series {$kode}: {$briefDesc} room '{$roomName}' (Total: {$totalInSeries} bookings, Dates: " . implode(', ', $dates) . ")";
    //         $insert->save();

    //         Booking::where('kode', $kode)->delete();

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => "Series '{$briefDesc}' deleted successfully",
    //             'data' => [
    //                 'kode_series' => $kode,
    //                 'total_deleted' => $totalInSeries,
    //                 'brief_description' => $briefDesc,
    //                 'room' => $roomName,
    //                 'dates' => $dates
    //             ]
    //         ]);

    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

}
