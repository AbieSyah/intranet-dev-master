<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance\BusinessTrip\BusinessTrip;
use App\Models\Attendance\BusinessTrip\BusinessTripApproval;
use App\Models\Attendance\BusinessTrip\BusinessTripCost;
use App\Models\Attendance\BusinessTrip\BusinessTripHotel;
use App\Models\Attendance\BusinessTrip\BusinessTripTransportation;
use App\Models\Attendance\BusinessTrip\BusinessTripLog;
use App\Models\Attendance\BusinessTripAllowance;
use App\Models\Employee;
use App\Models\Log;
use App\Models\Master\LineApproval;
use App\Notifications\AttendancePermitNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BulkLeaveApprovalNotification;

class BussinesTripApiController extends Controller
{
    public function getPendingBusinessTrips()
    {
        $employee = auth()->user()->employee;

        $businessTrips = BusinessTrip::with([
            'employee',
            'costs',
            'hotels',
            'transportations',
            'approvals.approver',
            'logs'
        ])
        ->where('employee_id', $employee->id)
        ->whereNotIn('status', ['completed', 'rejected', 'cancelled'])
        ->whereHas('approvals', function ($query) {
            $query->whereIn('status', ['waiting', 'pending', 'revised']);
        })
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            // 'message' => 'Data pengajuan perjalanan dinas yang masih proses approval',
            'data' => $businessTrips
        ]);
    }


    public function getApprovedBusinessTrips()
    {
        $employee = auth()->user()->employee;

        $businessTrips = BusinessTrip::with([
            'employee',
            'costs',
            'hotels',
            'transportations',
            'approvals.approver',
            'logs'
        ])
        ->where('employee_id', $employee->id)
        ->whereNotIn('status', ['waiting', 'pending', 'cancelled', 'revised'])
        ->whereHas('approvals', function ($query) {
            $query->whereIn('status', ['approved']);
        })
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            // 'message' => 'Data pengajuan perjalanan dinas yang masih proses approval',
            'data' => $businessTrips
        ]);
    }

    public function getMyBusinessTripAllowances()
    {
        $employee = auth()->user()->employee;

        $allowances = BusinessTripAllowance::with('level')
            ->whereHas('level', function ($query) use ($employee) {
                $query->where('nama', $employee->level->nama);
            })
            ->orderBy('category')
            ->get();

        return response()->json([
            'success' => true,
            // 'message' => 'Data business trip allowances sesuai posisi user',
            'data' => $allowances
        ]);
    }

    public function getBusinessTripsNeedMyApproval()
    {
        $employee = auth()->user()->employee;

        $businessTrips = BusinessTrip::with([
            'employee',
            'costs',
            'hotels',
            'transportations',
            'approvals.approver',
            'logs'
        ])
        ->whereHas('approvals', function ($query) use ($employee) {
            $query->where('approver_id', $employee->id)
                ->whereIn('status', ['waiting']);
        })
        ->whereNotIn('status', ['completed', 'cancelled', 'rejected'])
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            // 'message' => 'Data perjalanan dinas yang membutuhkan approval user',
            'data' => $businessTrips
        ]);
    }

    public function getApprovalHistory()
    {
        $employee = auth()->user()->employee;
        $businessTrips = BusinessTrip::whereHas('approvals', function ($query) use ($employee) {
            $query->where('approver_id', $employee->id)
                ->whereIn('status', ['approved', 'rejected']);
        })->with(['employee', 'approvals.approver'])
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json([
            'success' => true,
            'data' => $businessTrips
        ]);
    }


    public function getAllowanceDetail(Request $request)
    {
        $request->validate([
            'trip_type'   => 'required|in:domestic,overseas',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
        ]);

        $employee = auth()->user()->employee;
        $levelId = $employee->level_id;

        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        // Ambil daily allowance
        $allowances = BusinessTripAllowance::where('level_id', $levelId)
            ->where('trip_type', $request->trip_type)
            ->where('category', 'daily')
            ->get();

        // Tambahan laundry jika overseas dan total hari >= 7
        if ($request->trip_type === 'overseas' && $totalDays >= 7) {
            $laundry = BusinessTripAllowance::where('level_id', $levelId)
                ->where('trip_type', 'overseas')
                ->where('category', 'laundry')
                ->first();
            if ($laundry) {
                $allowances->push($laundry);
            }
        }

        // Format response dengan tambahan total_hari
        return response()->json([
            'success'    => true,
            'total_days' => $totalDays,
            'data'       => $allowances->map(function ($item) {
                return [
                    'id'       => $item->id,
                    'category' => $item->category,
                    'amount'   => $item->amount,
                    'currency' => $item->currency,
                    'trip_type' => $item->trip_type,
                ];
            })
        ]);
    }

    public function getApproverList(Request $request)
    {
        $request->validate([
            'trip_type' => 'required|in:domestic,overseas',
        ]);

        $employee = auth()->user()->employee;

        $approvalType = $request->trip_type === 'domestic'
            ? 'Business Trip Domestic'
            : 'Business Trip LuarNegeri';

        $lineApproval = LineApproval::where('approval_type', $approvalType)->first();

        if (!$lineApproval) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $ids = collect([
            $lineApproval->approve_1,
            $lineApproval->approve_2,
            $lineApproval->approve_3,
            $lineApproval->approve_4,
            $lineApproval->approve_5,
            $lineApproval->approve_6,
            $lineApproval->approve_7,
            $lineApproval->approve_8,
        ])->filter();

        $employees = Employee::whereIn('id', $ids)->pluck('fullname', 'id');

        $approvers = [];
        for ($i = 1; $i <= 8; $i++) {
            $field = "approve_$i";
            $approvers["approve_$i"] = $employees[$lineApproval->$field] ?? null;
        }

        return response()->json([
            'success' => true,
            'data'    => $approvers
        ]);
    }

    public function store(Request $request)
    {
        // Validasi input dari mobile
        $request->validate([
            'trip_type'           => 'required|in:domestic,overseas',
            'expense_method'      => 'required|in:reimbursement,advance,operating_cost',
            'start_date'          => 'required|date',
            'end_date'            => 'required|date|after_or_equal:start_date',
            'departure_time'      => 'nullable|string',
            'arrival_time'        => 'nullable|string',
            'departure_from'      => 'required|string|max:255',
            'arrival_to'          => 'required|string|max:255',
            'purpose'             => 'required|string',
            'advance_amount'      => 'nullable|numeric|min:0',
            'advance_currency'    => 'nullable|string|size:3',
            'need_hotel'          => 'nullable|boolean',
            'notes'               => 'nullable|string',
            // Allowances (dari master allowance)
            'allowances'          => 'nullable|array',
            'allowances.*.category' => 'required|string',
            'allowances.*.amount' => 'required|numeric',
            'allowances.*.currency' => 'nullable|string',
            // Manual expenses
            'manual_expenses'     => 'nullable|array',
            'manual_expenses.*.category' => 'required|string',
            'manual_expenses.*.qty' => 'required|integer|min:1',
            'manual_expenses.*.amount' => 'required|numeric',
            'manual_expenses.*.currency' => 'nullable|string',
            'manual_expenses.*.notes' => 'nullable|string',
            // Hotel (jika need_hotel = true)
            'hotel.reservation_by_ga' => 'nullable|boolean',
            'hotel.hotel_name'        => 'nullable|string',
            'hotel.check_in'          => 'nullable|date',
            'hotel.check_out'         => 'nullable|date|after_or_equal:hotel.check_in',
            'hotel.total_days'        => 'nullable|integer',
            'hotel.total_nights'      => 'nullable|integer',
            // Transportasi
            'transport_type'          => 'nullable|in:private,company_car,public_transport',
            'transport.vehicle_number' => 'nullable|string',
            'transport.driver_name'    => 'nullable|string',
            'transport.public_transport_type' => 'nullable|string',
            'transport.departure_date' => 'nullable|date',
            'transport.departure_time' => 'nullable|string',
            'transport.arrival_date'   => 'nullable|date',
            'transport.arrival_time'   => 'nullable|string',
            'transport.notes'          => 'nullable|string',
        ]);

        $employee = auth()->user()->employee;

        // Cek duplikasi tanggal
        $existingTrip = BusinessTrip::where('employee_id', $employee->id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                    });
            })
            ->whereNotIn('status', ['rejected', 'cancelled'])
            ->first();

        if ($existingTrip) {
            return response()->json([
                'success' => false,
                'message' => "Anda sudah memiliki pengajuan perjalanan dinas pada tanggal "
                    . Carbon::parse($existingTrip->start_date)->format('d M Y') . " sampai "
                    . Carbon::parse($existingTrip->end_date)->format('d M Y')
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Generate nomor dokumen
            $documentNumber = $this->generateDocumentNumber($request->trip_type);

            // Hitung total hari
            $startDate = Carbon::parse($request->start_date);
            $endDate   = Carbon::parse($request->end_date);
            $totalDays = $startDate->diffInDays($endDate) + 1;

            // Simpan data utama
            $businessTrip = BusinessTrip::create([
                'employee_id'        => $employee->id,
                'level'              => $employee->level->nama ?? '-',
                'position'           => $employee->position->nama ?? '-',
                'department'         => $employee->department->name ?? '-',
                'no_document'        => $documentNumber,
                'trip_type'          => $request->trip_type,
                'propose_date'       => now(),
                'start_date'         => $request->start_date,
                'end_date'           => $request->end_date,
                'total_days'         => $totalDays,
                'departure_time'     => $request->departure_time,
                'arrival_time'       => $request->arrival_time,
                'departure_from'     => $request->departure_from,
                'arrival_to'         => $request->arrival_to,
                'purpose'            => $request->purpose,
                'status'             => 'draft',
                'expense_method'     => $request->expense_method,
                'advance_amount'     => $request->advance_amount ?? 0,
                'advance_currency'   => $request->advance_currency ?? 'IDR',
                'need_hotel'         => $request->need_hotel ?? false,
                'notes'              => $request->notes,
                'updated_by'         => $employee->fullname,
            ]);

            $totalCost = 0;

            // Simpan allowances (daily, laundry)
            if ($request->has('allowances')) {
                foreach ($request->allowances as $item) {
                    $unitAmount = (float) $item['amount'];
                    $category   = $item['category'];
                    $totalUnit  = in_array($category, ['daily', 'laundry']) ? $totalDays : 1;
                    $totalAmount = $unitAmount * $totalUnit;

                    BusinessTripCost::create([
                        'business_trip_id' => $businessTrip->id,
                        'category'         => $category,
                        'qty'              => $totalUnit,
                        'unit_amount'      => $unitAmount,
                        'total_amount'     => $totalAmount,
                        'currency'         => $item['currency'] ?? 'IDR',
                        'notes'            => $item['notes'] ?? null,
                    ]);
                    $totalCost += $totalAmount;
                }
            }

            // Simpan manual expenses
            if ($request->has('manual_expenses')) {
                foreach ($request->manual_expenses as $item) {
                    $unitAmount = (float) $item['amount'];
                    $qty        = (int) $item['qty'];
                    $category   = $item['category'];
                    $totalAmount = $unitAmount * $qty;

                    BusinessTripCost::create([
                        'business_trip_id' => $businessTrip->id,
                        'category'         => $category,
                        'qty'              => $qty,
                        'unit_amount'      => $unitAmount,
                        'total_amount'     => $totalAmount,
                        'currency'         => $item['currency'] ?? 'IDR',
                        'notes'            => $item['notes'] ?? null,
                    ]);
                    $totalCost += $totalAmount;
                }
            }

            // Simpan hotel jika perlu
            if ($request->need_hotel && $request->has('hotel')) {
                BusinessTripHotel::create([
                    'business_trip_id'   => $businessTrip->id,
                    'reservation_by_ga'  => $request->hotel['reservation_by_ga'] ?? false,
                    'hotel_name'         => $request->hotel['hotel_name'] ?? null,
                    'check_in'           => $request->hotel['check_in'] ?? null,
                    'check_out'          => $request->hotel['check_out'] ?? null,
                    'total_days'         => $request->hotel['total_days'] ?? 0,
                    'total_nights'       => $request->hotel['total_nights'] ?? 0,
                ]);
            }

            // Simpan transportasi
            if ($request->filled('transport_type')) {
                $transportData = [
                    'business_trip_id' => $businessTrip->id,
                    'transport_type'   => $request->transport_type,
                ];

                if ($request->transport_type === 'company_car') {
                    $transportData['vehicle_number'] = $request->transport['vehicle_number'] ?? null;
                    $transportData['driver_name']    = $request->transport['driver_name'] ?? null;
                }

                if ($request->transport_type === 'public_transport') {
                    $transportData['public_transport_type'] = $request->transport['public_transport_type'] ?? null;
                    if (in_array($transportData['public_transport_type'], ['plane', 'train'])) {
                        $transportData['departure_date'] = $request->transport['departure_date'] ?? null;
                        $transportData['departure_time'] = $request->transport['departure_time'] ?? null;
                        $transportData['arrival_date']   = $request->transport['arrival_date'] ?? null;
                        $transportData['arrival_time']   = $request->transport['arrival_time'] ?? null;
                    }
                    if ($transportData['public_transport_type'] === 'other') {
                        $transportData['notes'] = $request->transport['notes'] ?? null;
                    }
                }

                BusinessTripTransportation::create($transportData);
            }

            // Update total cost ke business trip
            $businessTrip->update(['total_cost' => $totalCost]);

            // Buat approval berdasarkan LineApproval
            $approvalType = $request->trip_type === 'domestic'
                ? 'Business Trip Domestic'
                : 'Business Trip LuarNegeri';

            $lineApproval = $employee->lineApprovals()
                ->where('approval_type', $approvalType)
                ->first();

            if (!$lineApproval) {
                throw new \Exception('Line approval tidak ditemukan');
            }

            $approverIds = collect([
                $lineApproval->approve_1,
                $lineApproval->approve_2,
                $lineApproval->approve_3,
                $lineApproval->approve_4,
                $lineApproval->approve_5,
                $lineApproval->approve_6,
                $lineApproval->approve_7,
                $lineApproval->approve_8,
            ])->filter()->values();

            foreach ($approverIds as $index => $approverId) {
                $approver = Employee::with(['position', 'department'])->find($approverId);
                if (!$approver) continue;

                BusinessTripApproval::create([
                    'business_trip_id' => $businessTrip->id,
                    'approver_id'      => $approver->id,
                    'position'         => $approver->position->nama ?? '-',
                    'department'       => $approver->department->name ?? '-',
                    'level'            => $index + 1,
                    'status'           => $index === 0 ? 'waiting' : 'pending',
                    'approval_token'   => Str::uuid(),
                ]);
            }

            // Kirim notifikasi ke approval pertama
            $firstApproval = BusinessTripApproval::with('approver.user')
                ->where('business_trip_id', $businessTrip->id)
                ->where('level', 1)
                ->first();

            if ($firstApproval && $firstApproval->approver?->user) {
                $details = [
                    'greeting'    => 'Hi ' . $firstApproval->approver->fullname,
                    'subject'     => 'Pengajuan Business Trip',
                    'lines'       => [
                        'Karyawan mengajukan perjalanan dinas',
                        'Nama : ' . $employee->fullname,
                        'Tipe Trip : ' . ucfirst($businessTrip->trip_type),
                        'Tujuan : ' . $businessTrip->arrival_to,
                        'Tanggal : ' . Carbon::parse($businessTrip->start_date)->format('d M Y')
                            . ' - ' . Carbon::parse($businessTrip->end_date)->format('d M Y'),
                        'Nomor Dokumen : ' . $businessTrip->no_document,
                    ],
                    'actionText'  => 'Lihat Pengajuan',
                    'actionURL'   => route('business-trip.approval', ['token' => $firstApproval->approval_token]) . '#pill-approval',
                    'thanks'      => 'Terimakasih'
                ];
                $firstApproval->approver->user->notify(new AttendancePermitNotification($details));
            }

            // Catat log
            Log::create([
                'user_id'     => Auth::id(),
                'ip_address'  => $request->ip(),
                'action'      => 'insert',
                'description' => "{$employee->fullname} create new Business Trip {$businessTrip->trip_type}/{$businessTrip->start_date}-{$businessTrip->end_date}"
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Business trip berhasil diajukan',
                'data'    => $businessTrip->load(['costs', 'hotels', 'transportations', 'approvals'])
            ], 201);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $businessTrip = BusinessTrip::findOrFail($id);
        $employee = auth()->user()->employee;

        // Hanya pemilik yang boleh update, dan hanya status revised
        if ($businessTrip->employee_id != $employee->id || $businessTrip->status != 'revised') {
            return response()->json(['success' => false, 'message' => 'Tidak diizinkan'], 403);
        }

        // Validasi (sama seperti store mobile)
        $request->validate([
            'trip_type'           => 'required|in:domestic,overseas',
            'expense_method'      => 'required|in:reimbursement,advance,operating_cost',
            'start_date'          => 'required|date',
            'end_date'            => 'required|date|after_or_equal:start_date',
            'departure_time'      => 'nullable|string',
            'arrival_time'        => 'nullable|string',
            'departure_from'      => 'required|string|max:255',
            'arrival_to'          => 'required|string|max:255',
            'purpose'             => 'required|string',
            'advance_amount'      => 'nullable|numeric|min:0',
            'advance_currency'    => 'nullable|string|size:3',
            'need_hotel'          => 'nullable|boolean',
            'notes'               => 'nullable|string',
            'allowances'          => 'nullable|array',
            'allowances.*.category' => 'required|string',
            'allowances.*.amount' => 'required|numeric',
            'allowances.*.currency' => 'nullable|string',
            'manual_expenses'     => 'nullable|array',
            'manual_expenses.*.category' => 'required|string',
            'manual_expenses.*.qty' => 'required|integer|min:1',
            'manual_expenses.*.amount' => 'required|numeric',
            'manual_expenses.*.currency' => 'nullable|string',
            'manual_expenses.*.notes' => 'nullable|string',
            'hotel.reservation_by_ga' => 'nullable|boolean',
            'hotel.hotel_name'        => 'nullable|string',
            'hotel.check_in'          => 'nullable|date',
            'hotel.check_out'         => 'nullable|date|after_or_equal:hotel.check_in',
            'hotel.total_days'        => 'nullable|integer',
            'hotel.total_nights'      => 'nullable|integer',
            'transport_type'          => 'nullable|in:private,company_car,public_transport',
            'transport.vehicle_number' => 'nullable|string',
            'transport.driver_name'    => 'nullable|string',
            'transport.public_transport_type' => 'nullable|string',
            'transport.departure_date' => 'nullable|date',
            'transport.departure_time' => 'nullable|string',
            'transport.arrival_date'   => 'nullable|date',
            'transport.arrival_time'   => 'nullable|string',
            'transport.notes'          => 'nullable|string',
        ]);

        // Cek overlap tanggal (tidak dengan dirinya sendiri)
        $existingTrip = BusinessTrip::where('employee_id', $employee->id)
            ->where('id', '!=', $businessTrip->id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_date', '<=', $request->start_date)
                        ->where('end_date', '>=', $request->end_date);
                    });
            })
            ->whereNotIn('status', ['rejected', 'cancelled'])
            ->first();

        if ($existingTrip) {
            return response()->json([
                'success' => false,
                'message' => "Anda sudah memiliki pengajuan perjalanan dinas pada tanggal "
                    . Carbon::parse($existingTrip->start_date)->format('d M Y') . " sampai "
                    . Carbon::parse($existingTrip->end_date)->format('d M Y')
            ], 422);
        }

        DB::beginTransaction();
        try {
            $startDate = Carbon::parse($request->start_date);
            $endDate   = Carbon::parse($request->end_date);
            $totalDays = $startDate->diffInDays($endDate) + 1;

            // Update data utama
            $businessTrip->update([
                'trip_type'          => $request->trip_type,
                'expense_method'     => $request->expense_method,
                'start_date'         => $request->start_date,
                'end_date'           => $request->end_date,
                'total_days'         => $totalDays,
                'departure_time'     => $request->departure_time,
                'arrival_time'       => $request->arrival_time,
                'departure_from'     => $request->departure_from,
                'arrival_to'         => $request->arrival_to,
                'purpose'            => $request->purpose,
                'advance_amount'     => $request->advance_amount ?? 0,
                'advance_currency'   => $request->advance_currency ?? 'IDR',
                'need_hotel'         => $request->need_hotel ?? false,
                'notes'              => $request->notes,
                'status'             => 'draft',         // setelah update jadi draft
                // 'revised_level'      => null,            // reset revised level
                // 'revised_count'      => ($businessTrip->revised_count ?? 0) + 1,
                'updated_by'         => $employee->fullname,
            ]);

            // Hapus detail lama
            $businessTrip->costs()->delete();
            $businessTrip->transportations()->delete();
            $businessTrip->hotels()->delete();

            $totalCost = 0;

            if ($request->allowances) {
                foreach ($request->allowances as $item) {
                    $unitAmount     = (float) $item['amount'];
                    $category       = $item['category'];
                    $totalUnit      = in_array($category, [ 'daily', 'laundry' ]) ? $totalDays : 1;
                    $totalAmount    = $unitAmount * $totalUnit;
                    BusinessTripCost::create([
                        'business_trip_id'  => $businessTrip->id,
                        'category'          => $category,
                        'unit_amount'       => $unitAmount,
                        'qty'               => $totalUnit,
                        'total_amount'      => $totalAmount,
                        'currency'          => $item['currency'] ?? 'IDR',
                        'notes'             => $item['notes'] ?? null,
                    ]);
                    $totalCost += $totalAmount;
                }
            }
            // ================= MANUAL EXPENSE =================
            if ($request->manual_expenses) {
                foreach ($request->manual_expenses as $item) {
                    $unitAmount     = $this->cleanCurrency( $item['amount'] ?? 0 );
                    $qty            = (int) ($item['qty'] ?? 1);
                    $category       = $item['category'] ?? null;
                    if (!$category) {
                        continue;
                    }
                    $totalAmount = $unitAmount * $qty;
                    BusinessTripCost::create([
                        'business_trip_id'  => $businessTrip->id,
                        'category'          => $category,
                        'unit_amount'       => $unitAmount,
                        'qty'               => $qty,
                        'total_amount'      => $totalAmount,
                        'currency'          => $item['currency'] ?? 'IDR',
                        'notes'             => $item['notes'] ?? null,
                    ]);
                    $totalCost += $totalAmount;
                }
            }
            // ================= HOTEL =================
            if ($request->need_hotel == 1) {
                BusinessTripHotel::create([
                    'business_trip_id'  => $businessTrip->id,
                    'reservation_by_ga' => $request->reservation_by_ga ?? 0,
                    'hotel_name'        => $request->hotel_name,
                    'check_in'          => $request->check_in,
                    'check_out'         => $request->check_out,
                    'total_days'        => $request->Days_checkIn ?? 0,
                    'total_nights'      => $request->Night_checkIn ?? 0,
                ]);
            }
            // ================= TRANSPORT =================
            if ($request->transport_type) {
                $transportData = [
                    'business_trip_id'  => $businessTrip->id,
                    'transport_type'    => $request->transport_type,
                ];
                if ( $request->transport_type === 'company_car')
                {
                    $transportData['vehicle_number'] = $request->vehicle_number;
                    $transportData['driver_name'] = $request->driver_name;
                }
                if ( $request->transport_type === 'public_transport' )
                {
                $transportData['public_transport_type'] = $request->public_transport_type;
                if ( in_array( $request->public_transport_type, ['plane', 'train']))
                {
                    $transportData['departure_date']    = $request->transport_start_date;
                    $transportData['departure_time']    = $request->transport_departure_time;
                    $transportData['arrival_date']      = $request->transport_end_date;
                    $transportData['arrival_time']      = $request->transport_arrival_time;
                } if ($request->public_transport_type === 'other')
                    {
                        $transportData['notes'] = $request->transport_notes;
                    }
                }
                BusinessTripTransportation::create($transportData);
            }

            $businessTrip->update(['total_cost' => $totalCost]);

            // ================= RESET APPROVAL (SAMA SEPERTI WEB) =================
            $revisedLevel = $businessTrip->getOriginal('revised_level'); // simpan sebelum di-null
            if (!is_null($revisedLevel)) {
                // Set approval pada level revisi menjadi waiting
                BusinessTripApproval::where('business_trip_id', $businessTrip->id)
                    ->where('level', $revisedLevel)
                    ->update([
                        'status'      => 'waiting',
                        'approved_at' => null,
                    ]);

                // Set approval level setelahnya menjadi pending
                BusinessTripApproval::where('business_trip_id', $businessTrip->id)
                    ->where('level', '>', $revisedLevel)
                    ->update([
                        'status'      => 'pending',
                        'approved_at' => null,
                    ]);

                // Kirim notifikasi ke approver yang merevisi (level yang direvisi)
                $approval = BusinessTripApproval::with('approver.user')
                    ->where('business_trip_id', $businessTrip->id)
                    ->where('level', $revisedLevel)
                    ->first();

                if ($approval && $approval->approver?->user) {
                    $details = [
                        'greeting'    => 'Hi ' . $approval->approver->fullname,
                        'subject'     => 'Business Trip Revised',
                        'lines'       => [
                            'Business Trip telah direvisi oleh pemohon',
                            'Nama : ' . $businessTrip->employee->fullname,
                            'Tujuan : ' . $businessTrip->arrival_to,
                            'Tanggal : ' . Carbon::parse($businessTrip->start_date)->format('d M Y') . ' - ' . Carbon::parse($businessTrip->end_date)->format('d M Y'),
                            'Silakan periksa kembali pengajuan.'
                        ],
                        'actionText'  => 'Lihat Pengajuan',
                        'actionURL'   => route('business-trip.approval', ['token' => $approval->approval_token]) . '#pill-approval',
                        'thanks'      => 'Terimakasih'
                    ];
                    $approval->approver->user->notify(new AttendancePermitNotification($details));
                }
            }

            // Log
            Log::create([
                'user_id'     => auth()->id(),
                'ip_address'  => $request->ip(),
                'action'      => 'update',
                'description' => "{$employee->fullname} update Business Trip {$businessTrip->trip_type}/{$businessTrip->start_date}-{$businessTrip->end_date}"
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil diperbarui',
                'data'    => $businessTrip->load(['costs', 'hotels', 'transportations', 'approvals'])
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal update: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateDocNumber(Request $request)
    {
        $request->validate([
            'trip_type' => 'required|in:domestic,overseas'
        ]);

        $documentNumber = $this->generateDocumentNumber($request->trip_type);

        return response()->json([
            'success' => true,
            'no_document' => $documentNumber
        ]);
    }

    private function generateDocumentNumber(string $tripType)
    {
        $prefix = $tripType === 'domestic' ? 'BTD' : 'BTO';
        $period = now()->format('Ym');
        $lastTrip = BusinessTrip::where('no_document', 'like', "{$prefix}/{$period}/%")
            ->latest('id')->first();
        $sequence = $lastTrip ? intval(substr($lastTrip->no_document, -4)) + 1 : 1;
        return sprintf("%s/%s/%04d", $prefix, $period, $sequence);
    }

    private function cleanCurrency($value)
    {
        return (int) preg_replace(
            '/[^\d]/',
            '',
            $value
        );
    }

    // ========== PROSES APPROVAL (APPROVE/REJECT/REVISE) ==========
    public function HandleApproval(Request $request)
    {
        $request->validate([
            'approval_id' => 'required|exists:business_trip_approvals,id',
            'action'      => 'required|in:approved,rejected,revised',
            'reason'      => 'nullable|string|required_if:action,rejected,revised',
        ]);

        $employee = auth()->user()->employee;

        DB::beginTransaction();

        try {

            $approval = BusinessTripApproval::with([
                'businessTrip.employee.user',
                'approver.user'
            ])->find($request->approval_id);

            if (!$approval) {
                throw new \Exception('Data approval tidak ditemukan');
            }

            if ($approval->status !== 'waiting') {
                return response()->json([
                    'success' => false,
                    'message' => 'Approval sudah diproses'
                ], 422);
            }

            $businessTrip = $approval->businessTrip;
            $nextApproverEmails = [];

            // ================= REJECT =================
            if ($request->action === 'rejected') {

                $businessTrip->update([
                    'status'     => 'rejected',
                    'updated_by' => $employee->fullname,
                ]);

                $approval->update([
                    'status'      => 'rejected',
                    'approved_at' => now(),
                ]);

                $this->logApprovalAction(
                    $businessTrip->id,
                    $approval->id,
                    'rejected',
                    $request->reason
                );
            }

            // ================= REVISE =================
            elseif ($request->action === 'revised') {

                $businessTrip->update([
                    'status'        => 'revised',
                    'updated_by'    => $employee->fullname,
                    'revised_level' => $approval->level,
                    'revised_count' => ($businessTrip->revised_count ?? 0) + 1,
                ]);

                $approval->update([
                    'status' => 'revised',
                ]);

                $this->logApprovalAction(
                    $businessTrip->id,
                    $approval->id,
                    'revised',
                    $request->reason
                );
            }

            // ================= APPROVE =================
            else {

                $approval->update([
                    'status'      => 'approved',
                    'approved_at' => now(),
                ]);

                $this->logApprovalAction(
                    $businessTrip->id,
                    $approval->id,
                    'approved',
                    $request->reason
                );

                $nextApproval = BusinessTripApproval::where(
                    'business_trip_id',
                    $businessTrip->id
                )
                ->where('level', '>', $approval->level)
                ->orderBy('level')
                ->first();

                if ($nextApproval) {

                    if ($nextApproval->status !== 'waiting') {
                        $nextApproval->update([
                            'status' => 'waiting'
                        ]);
                    }

                    $approver = $nextApproval->approver;

                    if ($approver?->user?->email) {

                        $email = $approver->user->email;

                        $nextApproverEmails[$email] = [
                            'approver_name' => $approver->fullname,
                            'requests' => [[
                                'text' =>
                                    $businessTrip->employee->fullname .
                                    ' | ' .
                                    ucfirst($businessTrip->trip_type) .
                                    ' | ' .
                                    Carbon::parse($businessTrip->start_date)->format('d M Y') .
                                    ' - ' .
                                    Carbon::parse($businessTrip->end_date)->format('d M Y') .
                                    ' | ' .
                                    $businessTrip->departure_from .
                                    ' → ' .
                                    $businessTrip->arrival_to,
                                'token' => $nextApproval->approval_token,
                            ]]
                        ];
                    }

                } else {

                    // FINAL APPROVED
                    $businessTrip->update([
                        'status'      => 'approved',
                        'approved_at' => now(),
                        'updated_by'  => $employee->fullname,
                    ]);

                    $emp = Employee::with([
                        'area',
                        'department',
                        'position',
                        'groupEmployees.groupEmployeeWorkhour.groupWorkHours.workhour.details'
                    ])->find($businessTrip->employee_id);

                    if ($emp) {
                        $this->generateAttendance(
                            $emp,
                            $businessTrip->start_date,
                            $businessTrip->end_date,
                            $businessTrip->trip_type,
                            $businessTrip->id
                        );
                    }
                }
            }

            // ================= SEND EMAIL =================
            foreach ($nextApproverEmails as $email => $data) {

                $payload = [
                    'subject'    => 'Permintaan Business Trip Menunggu Approval',
                    'greeting'   => 'Hi ' . $data['approver_name'],
                    'requests'   => $data['requests'],
                    'actionText' => 'Lihat Pengajuan',
                    'actionURL'  => route('business-trip.approval', [
                        'token' => $data['requests'][0]['token']
                    ]) . '#pill-approval',
                    'thanks'     => 'Terimakasih',
                ];

                Notification::route('mail', $email)
                    ->notify(new BulkLeaveApprovalNotification($payload));
            }

            $user = Auth::user();

            Log::create([
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
                'action' => 'insert',
                'description' =>
                    "{$user->employee->fullname} Melakukan {$request->action} " .
                    "{$businessTrip->trip_type}/{$businessTrip->start_date}-{$businessTrip->end_date}"
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => ucfirst($request->action) . ' berhasil diproses.',
            ], 200);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function logApprovalAction($tripId, $approvalId, $status, $reason = null)
    {
        BusinessTripLog::create([
            'business_trip_id' => $tripId,
            'approval_path_id' => $approvalId,
            'status'           => $status,
            'reason'           => $reason,
            'action_at'        => now(),
        ]);
    }

    private function generateAttendance($emp, $start, $end, $type, $businessTripId = null)
    {
        $startDate = Carbon::parse($start);
        $endDate   = $end ? Carbon::parse($end) : $startDate;

        while ($startDate->lte($endDate)) {
            $date    = $startDate->toDateString();
            $workhourData = $this->getWorkHourByDate($emp, $date);
            if (empty($workhourData)) {
                $startDate->addDay();
                continue;
            }

            $existing = DB::table('employee_attendances')
                ->where('employee_id', $emp->id)
                ->where('date', $date)
                ->first();

            if (!$existing || $existing->attendance_status !== 'present') {
                DB::table('employee_attendances')->updateOrInsert(
                    [
                        'employee_id' => $emp->id,
                        'date'        => $date
                    ],
                    [
                        'area_name'         => $emp->area->name ?? '-',
                        'department_name'   => $emp->department->name ?? '-',
                        'position_name'     => $emp->position->nama ?? '-',
                        'group_id'          => $workhourData['group_id'] ?? null,
                        'master_workhour_id'=> $workhourData['master_workhour_id'] ?? null,
                        'work_in'           => $workhourData['work_in'] ?? null,
                        'work_out'          => $workhourData['work_out'] ?? null,
                        'business_trip_id'  => $businessTripId,
                        'attendance_status' => 'Business trip',
                        'source'            => "Business Trip - $type",
                        'created_by'        => auth()->user()->employee->fullname ?? 'System',
                        'updated_by'        => auth()->user()->employee->fullname ?? 'System',
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]
                );
            }

            $startDate->addDay();
        }
    }

    private function getWorkHourByDate($employee, $date)
    {
        $groupEmployee = $employee->groupEmployees->first();
        if (!$groupEmployee || !$groupEmployee->groupEmployeeWorkhour) {
            return [];
        }
        $targetDate = Carbon::parse($date);
        $groupWorkhour = $groupEmployee->groupEmployeeWorkhour;
        $activeGroupWorkHour = $groupWorkhour->groupWorkHours
            ->filter(function ($g) use ($targetDate) {
                if (!$g->start_date) return false;
                $start = Carbon::parse($g->start_date);
                $end   = $g->end_date ? Carbon::parse($g->end_date) : null;
                return $start->lte($targetDate) && (!$end || $end->gte($targetDate));
            })
            ->sortByDesc(fn($g) => Carbon::parse($g->start_date)->timestamp)
            ->first();

        if (!$activeGroupWorkHour || !$activeGroupWorkHour->workhour) {
            return [];
        }
        $dayName = strtolower($targetDate->format('l'));
        $detail = $activeGroupWorkHour->workhour->details
            ->first(fn($d) => strtolower($d->day) === $dayName);
        if (!$detail) {
            return [];
        }
        return [
            'group_id'           => $groupWorkhour->id ?? null,
            'master_workhour_id' => $activeGroupWorkHour->workhour->id ?? null,
            'work_in'            => $detail->work_in ?? null,
            'work_out'           => $detail->work_out ?? null,
        ];
    }

}
