<?php

namespace App\Http\Controllers\Attendance\BusinessTrip;

use App\Http\Controllers\Controller;
use App\Models\Attendance\BusinessTrip\BusinessTrip;
use App\Models\Attendance\BusinessTrip\BusinessTripApproval;
use App\Models\Attendance\BusinessTrip\BusinessTripCost;
use App\Models\Attendance\BusinessTrip\BusinessTripHotel;
use App\Models\Attendance\BusinessTrip\BusinessTripTransportation;
use App\Models\Attendance\BusinessTripAllowance;
use App\Models\Employee;
use App\Models\Log;
use App\Models\Master\LineApproval;
use App\Notifications\AttendancePermitNotification;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ProposeController extends Controller
{
    public function generateDocumentNumberAjax(Request $request)
    {
        $documentNumber = $this->generateDocumentNumber(
            $request->trip_type
        );

        return response()->json([
            'no_document' => $documentNumber
        ]);
    }
    private function generateDocumentNumber($tripType)
    {
        $prefix = $tripType === 'domestic'
            ? 'BTD'
            : 'BTO';

        // format : 202605
        $period = now()->format('Ym');

        // ambil document terakhir bulan ini
        $lastTrip = BusinessTrip::where(
                'no_document',
                'like',
                "{$prefix}/{$period}/%"
            )
            ->latest('id')
            ->first();

        $sequence = 1;

        if ($lastTrip) {

            // ambil angka terakhir
            $explode = explode('/', $lastTrip->no_document);

            $lastSequence = (int) end($explode);

            $sequence = $lastSequence + 1;
        }

        return sprintf(
            '%s/%s/%04d',
            $prefix,
            $period,
            $sequence
        );
    }
    public function getApprover(Request $request)
    {
        $user = auth()->user();

        $approvalType =
            $request->trip_type === 'domestic'
                ? 'Business Trip Domestic'
                : 'Business Trip LuarNegeri';

        $approval = LineApproval::with([
            'approve1',
            'approve2',
            'approve3',
            'approve4',
            'approve5',
            'approve6',
            'approve7',
            'approve8',
        ])
        ->where('approval_type', $approvalType)
        ->first();

        if (!$approval) {
            return response()->json([]);
        }

        return response()->json([
            'approve_1' => optional($approval->approve1)->fullname,
            'approve_2' => optional($approval->approve2)->fullname,
            'approve_3' => optional($approval->approve3)->fullname,
            'approve_4' => optional($approval->approve4)->fullname,
            'approve_5' => optional($approval->approve5)->fullname,
            'approve_6' => optional($approval->approve6)->fullname,
            'approve_7' => optional($approval->approve7)->fullname,
            'approve_8' => optional($approval->approve8)->fullname,
        ]);
    }
public function getAllowance(Request $request)
    {
        $employee = auth()->user()->employee;

        $tripType = $request->trip_type;

        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);

        $totalDays = $startDate->diffInDays($endDate) + 1;
        // DAILY otomatis
        $allowances = BusinessTripAllowance::query()
            ->where('level_id', $employee->level_id)
            ->where('trip_type', $tripType)
            ->where('category', 'daily')
            ->get();

        // TAMBAHAN LAUNDRY
        if ($tripType === 'overseas' && $totalDays >= 7) {

            $laundry = BusinessTripAllowance::query()
                ->where('level_id', $employee->level_id)
                ->where('trip_type', 'overseas')
                ->where('category', 'laundry')
                ->first();
            // dd($laundry);
            if ($laundry) {
                $allowances->push($laundry);
            }
        }

        return response()->json([
            'total_days' => $totalDays,
            'data' => $allowances
        ]);
    }
    public function create()
    {
        $user = auth()->user();
        $employee = $user->employee;
        $levelId = $employee->level_id;
        // kosong untuk halaman create
        $manualExpenses = collect();
        $allowances = BusinessTripAllowance::where(
                'level_id',
                $levelId
            )
            ->where('category', 'daily')
            ->orderBy('trip_type')
            ->orderBy('category')
            ->get();
        $approvers = [];
        $lineApproval = $employee->lineApprovals()
            ->where('approval_type', 'Business Trip')
            ->first();
        if ($lineApproval) {
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
            $employees = Employee::whereIn('id', $ids)
                ->pluck('fullname', 'id');
            for ($i = 1; $i <= 8; $i++) {
                $field = "approve_$i";
                $approvers[$field] =
                    $employees[$lineApproval->$field] ?? null;
            }
        }

        return view(
            'pages.profile.Attendance.business-trip.form',
            compact(
                'user',
                'approvers',
                'allowances',
                'manualExpenses'
            )
        );
    }
    public function store(Request $request)
    {
        $existingTrip = BusinessTrip::where('employee_id', auth()->user()->employee->id)
            ->where(function ($query) use ($request) {

                $query->whereBetween('start_date', [
                    $request->start_date,
                    $request->end_date
                ])
                ->orWhereBetween('end_date', [
                    $request->start_date,
                    $request->end_date
                ])
                ->orWhere(function ($q) use ($request) {

                    $q->where('start_date', '<=', $request->start_date)
                    ->where('end_date', '>=', $request->end_date);
                });
            })
            ->whereNotIn('status', ['rejected', 'cancelled'])
            ->first();

        if ($existingTrip) {

            $start = Carbon::parse($existingTrip->start_date)
                ->format('d M Y');

            $end = Carbon::parse($existingTrip->end_date)
                ->format('d M Y');

            return response()->json([
                'success' => false,
                'message' =>
                    "Anda sudah memiliki pengajuan perjalanan dinas pada tanggal {$start} sampai {$end}"
            ], 422);
        }
        DB::beginTransaction();

        try {
            // ================================= VALIDATION =================================
            $request->validate([
                // MAIN
                'trip_type'           => 'required|in:domestic,overseas',
                'expense_method'      => 'required|in:reimbursement,advance,operating_cost',
                // 'document_number'     => 'required|unique:business_trips,no_document',

                'start_date'          => 'required|date',
                'end_date'            => 'required|date|after_or_equal:start_date',

                'departure_time'      => 'nullable',
                'arrival_time'        => 'nullable',

                'departure_from'      => 'required|string|max:255',
                'arrival_to'          => 'required|string|max:255',

                'purpose'             => 'required|string',
                // ADVANCE
                'advance_amount'      => 'nullable|numeric|min:0',
                // HOTEL
                'need_hotel'          => 'nullable',
                // ALLOWANCE
                'allowances'          => 'nullable|array',
                // MANUAL EXPENSE
                'manual_expenses'     => 'nullable|array',
                // TRANSPORT
                'transport_type'      => 'nullable|string',

            ]);
            $documentNumber = $this->generateDocumentNumber(
                $request->trip_type
            );
            // dd($documentNumber);
            $employee = auth()->user()->employee;
            // ================================= TOTAL DAYS =================================
            $startDate = Carbon::parse($request->start_date);
            $endDate   = Carbon::parse($request->end_date);
            $totalDays = $startDate->diffInDays($endDate) + 1;
            // ================================= CREATE BUSINESS TRIP =================================
            $businessTrip = BusinessTrip::create([
                'employee_id'        => $employee->id,
                // SNAPSHOT
                'level'              => $employee->level->nama ?? '-',
                'position'           => $employee->position->nama ?? '-',
                'department'         => $employee->department->name ?? '-',
                // DOCUMENT
                'no_document'        => $documentNumber,
                'trip_type'          => $request->trip_type ?? '-',
                // DATE
                'propose_date'       => now(),
                'start_date'         => $request->start_date,
                'end_date'           => $request->end_date,
                'total_days'         => $totalDays,
                // TIME
                'departure_time'     => $request->departure_time,
                'arrival_time'       => $request->arrival_time,
                // LOCATION
                'departure_from'     => $request->departure_from,
                'arrival_to'         => $request->arrival_to,
                // PURPOSE
                'purpose'            => $request->purpose,
                // STATUS
                'status'             => 'draft',
                // EXPENSE
                'expense_method'     => $request->expense_method,
                // ADVANCE
                'advance_amount'     => $request->advance_amount ?? 0,
                'advance_currency'   => $request->advance_currency ?? 'IDR',
                // HOTEL
                'need_hotel'         => $request->need_hotel ?? 0,
                // NOTES
                'notes'              => $request->notes,
            ]);
            // ================================= ALLOWANCE =================================
            $totalCost = 0;
            if ($request->allowances) {
                foreach ($request->allowances as $item) {

                    $unitAmount = (float) $item['amount'];
                    $category   = $item['category'];

                    if (in_array($category, ['daily', 'laundry'])) {
                        $totalUnit = $totalDays;
                    } else {

                        $totalUnit = 1;
                    }

                    $total_amount = $unitAmount * $totalUnit;

                    BusinessTripCost::create([
                        'business_trip_id' => $businessTrip->id,
                        'category'         => $category,
                        'unit_amount'      => $unitAmount,
                        'qty'              => $totalUnit,
                        'total_amount'     => $total_amount,
                        'currency'         => $item['currency'] ?? 'IDR',
                        'notes'            => $item['notes'] ?? null,
                    ]);
                    $totalCost += $total_amount;
                }
            }
            // ================================= MANUAL EXPENSE =================================
            if ($request->manual_expenses) {
                foreach ($request->manual_expenses as $item) {

                    $unitAmount = (float) str_replace('.', '', $item['amount'] ?? 0);
                    $qty = (int) ($item['qty'] ?? 1);
                    $category = $item['category'] ?? null;

                    if (!$category) {
                        continue;
                    }

                    $total_amount = $unitAmount * $qty;

                    BusinessTripCost::create([
                        'business_trip_id' => $businessTrip->id,
                        'category'         => $category,
                        'unit_amount'      => $unitAmount,
                        'qty'              => $qty,
                        'total_amount'     => $total_amount,
                        'currency'         => $item['currency'] ?? 'IDR',
                        'notes'            => $item['notes'] ?? null,
                    ]);
                    $totalCost += $total_amount;
                }
            }
            // ================================= HOTEL =================================
            if ($request->need_hotel == 1) {
                BusinessTripHotel::create([
                    'business_trip_id'     => $businessTrip->id,
                    'reservation_by_ga'    => $request->reservation_by_ga ?? 0,
                    'hotel_name'           => $request->hotel_name,
                    'check_in'             => $request->check_in,
                    'check_out'            => $request->check_out,
                    'total_days'           => $request->Days_checkIn ?? 0,
                    'total_nights'         => $request->Night_checkIn ?? 0,
                ]);
            }

            // ================================= TRANSPORT =================================
            if ($request->transport_type) {
                $transportData = [
                    'business_trip_id' => $businessTrip->id,
                    'transport_type'   => $request->transport_type,
                ];

                if ($request->transport_type === 'company_car') {

                    $transportData['vehicle_number'] =
                        $request->vehicle_number;

                    $transportData['driver_name'] =
                        $request->driver_name;
                }

                if ($request->transport_type === 'public_transport') {

                    $transportData['public_transport_type'] =
                        $request->public_transport_type;

                    if (
                        in_array(
                            $request->public_transport_type,
                            ['plane', 'train']
                        )
                    ) {

                        $transportData['departure_date'] =
                            $request->transport_start_date;

                        $transportData['departure_time'] =
                            $request->transport_departure_time;

                        $transportData['arrival_date'] =
                            $request->transport_end_date;

                        $transportData['arrival_time'] =
                            $request->transport_arrival_time;
                    }

                    if ($request->public_transport_type === 'other') {

                        $transportData['notes'] =
                            $request->transport_notes;
                    }
                }
                BusinessTripTransportation::create($transportData);
            }

            // ================================= APPROVAL =================================
            $approvalType =
                $request->trip_type === 'domestic'
                    ? 'Business Trip Domestic'
                    : 'Business Trip LuarNegeri';

            $lineApproval = $employee->lineApprovals()
                ->where('approval_type', $approvalType)
                ->first();

            if (!$lineApproval) {
                throw new \Exception(
                    'Line approval business trip tidak ditemukan'
                );
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
            ])
            ->filter()
            ->values();

            foreach ($approverIds as $index => $approverId) {

                $approver = Employee::with([
                    'position',
                    'department'
                ])->find($approverId);

                if (!$approver) {
                    continue;
                }

                BusinessTripApproval::create([
                    'business_trip_id' => $businessTrip->id,
                    'approver_id'      => $approver->id,
                    'position'         => $approver->position->nama ?? '-',
                    'department'       => $approver->department->name ?? '-',
                    'level'            => $index + 1,

                    'status' => $index === 0
                        ? 'waiting'
                        : 'pending',

                    'approval_token' => Str::uuid(),
                ]);
            }

            $firstApproval = BusinessTripApproval::with([
                'approver.user'
            ])
            ->where('business_trip_id', $businessTrip->id)
            ->where('level', 1)
            ->first();

            if (
                $firstApproval &&
                $firstApproval->approver?->user
            ) {

                $details = [
                    'greeting' =>
                        'Hi ' .
                        $firstApproval->approver->fullname,
                    'subject' =>
                        'Pengajuan Business Trip',
                    'lines' => [
                        'Karyawan mengajukan perjalanan dinas',
                        'Nama : ' . $employee->fullname,
                        'Tipe Trip : ' . ucfirst($businessTrip->trip_type),
                        'Tujuan : ' . $businessTrip->arrival_to,
                        'Tanggal : ' .
                            Carbon::parse($businessTrip->start_date)
                                ->format('d M Y')
                            . ' - ' .
                            Carbon::parse($businessTrip->end_date)
                                ->format('d M Y'),
                        'Nomor Dokumen : ' .
                            $businessTrip->no_document,
                    ],

                    'actionText' => 'Lihat Pengajuan',

                    'actionURL' => route(
                        'business-trip.approval',
                        [
                            'token' => $firstApproval->approval_token
                        ]
                    ) . '#pill-approval',

                    'thanks' => 'Terimakasih'
                ];

                $firstApproval->approver->user
                    ->notify(
                        new AttendancePermitNotification($details)
                    );
            }
             $businessTrip->update([
                'total_cost' => $totalCost
            ]);

            $user = Auth::user();
                Log::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'action' => 'insert',
                    'description' => "{$user->employee->fullname} create new Business Trip {$businessTrip->trip_type}/{$businessTrip->start_date}-{$businessTrip->end_date}"
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Business trip berhasil diajukan'
            ]);

        } catch (\Throwable $th) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $user = auth()->user();
        $id = decrypt($id);

        $businessTrip = BusinessTrip::with([
            'costs',
            'transportations',
            'hotels',
        ])->findOrFail($id);
        // hanya boleh edit revised
        if ($businessTrip->status !== 'revised') {
            abort(403);
        }
        $manualExpenses = $businessTrip->costs
        ->whereNotIn('category', ['daily', 'laundry']);

        return view(
            'pages.profile.Attendance.business-trip.form',
            compact('businessTrip','user','manualExpenses')
        );
    }
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $businessTrip = BusinessTrip::with([
                'costs',
                'transportations',
                'hotels',
                'approvals.approver.user'
            ])->findOrFail(decrypt($id));
            // dd($businessTrip);
            // ================= VALIDATION =================
            $request->validate([
                'trip_type'           => 'required|in:domestic,overseas',
                'expense_method'      => 'required|in:reimbursement,advance,operating_cost',
                'start_date'          => 'required|date',
                'end_date'            => 'required|date|after_or_equal:start_date',
                'departure_from'      => 'required|string|max:255',
                'arrival_to'          => 'required|string|max:255',
                'purpose'             => 'required|string',
                'advance_amount'      => 'nullable',
                'transport_type'      => 'nullable|string',
            ]);
            // ================= CHECK OVERLAP =================
            $existingTrip = BusinessTrip::where(
                    'employee_id',
                    auth()->user()->employee->id
                )
                ->where('id', '!=', $businessTrip->id)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_date', [
                            $request->start_date,
                            $request->end_date
                        ])
                        ->orWhereBetween('end_date', [
                            $request->start_date,
                            $request->end_date
                        ])
                        ->orWhere(function ($q) use ($request) {
                            $q->where(
                                    'start_date',
                                    '<=',
                                    $request->start_date
                                )
                                ->where(
                                    'end_date',
                                    '>=',
                                    $request->end_date
                                );
                        });
                })
                ->whereNotIn('status', [
                    'rejected',
                    'cancelled'
                ])
                ->first();
            if ($existingTrip) {
                $start = Carbon::parse(
                        $existingTrip->start_date
                    )->format('d M Y');
                $end = Carbon::parse(
                        $existingTrip->end_date
                    )->format('d M Y');
                return response()->json([
                    'success' => false,
                    'message' =>
                        "Anda sudah memiliki pengajuan perjalanan dinas pada tanggal {$start} sampai {$end}"
                ], 422);
            }
            // ================= TOTAL DAYS =================
            $startDate = Carbon::parse(
                $request->start_date
            );
            $endDate = Carbon::parse(
                $request->end_date
            );
            $totalDays = $startDate->diffInDays($endDate) + 1;
            // ================= UPDATE MAIN =================
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
                'advance_amount'     => $this->cleanCurrency( $request->advance_amount ),
                'advance_currency'   => $request->advance_currency ?? 'IDR',
                'need_hotel'         => $request->need_hotel ?? 0,
                'notes'              => $request->notes,
                // kembali ke draft
                'status'             => 'draft',
                // reset revised level
                // 'revised_level'      => null,
                // tambah revise count
                'revised_count'       => ($businessTrip->revised_count ?? 0) + 1,
            ]);
            // dd($businessTrip);
            // ================= DELETE OLD DETAILS =================
            $businessTrip->costs()->delete();
            $businessTrip->transportations()->delete();
            $businessTrip->hotels()->delete();
            // ================= ALLOWANCE =================
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
            // ================= UPDATE COST =================
            $businessTrip->update([
                'total_cost' => $totalCost
            ]);
            // ================= APPROVAL RESET =================
            BusinessTripApproval::where('business_trip_id', $businessTrip->id)
            ->update([
                'approved_at' => null
            ]);

            BusinessTripApproval::where('business_trip_id', $businessTrip->id)
            ->where('level', 1)
            ->update([
                'status' => 'waiting'
            ]);

            // approval setelahnya pending
            BusinessTripApproval::where('business_trip_id', $businessTrip->id)
                ->where('level', '>', 1)
                ->update([
                    'status' => 'pending',
                    'approved_at' => null,
                ]);

            // ================= SEND NOTIFICATION =================
            $approval = BusinessTripApproval::with(['approver.user'])
                ->where('business_trip_id', $businessTrip->id)
                ->where('level', $businessTrip->id)
                ->first();
                // dd($approval);

                if ($approval && $approval->approver?->user) {
                    $details = [
                        'greeting' => 'Hi ' . $approval->approver->fullname,
                        'subject' => 'Business Trip Revised',
                        'lines' => [
                            'Business Trip telah direvisi',
                            'Nama : ' . $businessTrip->employee->fullname,
                            'Tujuan : ' . $businessTrip->arrival_to,
                            'Tanggal : ' . Carbon::parse($businessTrip->start_date)->format('d M Y') . ' - ' . Carbon::parse($businessTrip->end_date)->format('d M Y'),
                        ],
                        'actionText' => 'Lihat Pengajuan',
                        'actionURL' => route('business-trip.approval', ['token' => $approval->approval_token]) . '#pill-approval',
                        'thanks' => 'Terimakasih'
                    ];

                    $approval->approver->user->notify(new AttendancePermitNotification($details));
                }
            // ================= LOG =================
            $user = Auth::user();
            Log::create([
                'user_id'       => $user->id,
                'ip_address'    => $request->ip(),
                'action'        => 'update',
                'description'   => "{$user->employee->fullname} update Business Trip {$businessTrip->trip_type}/{$businessTrip->start_date}-{$businessTrip->end_date}"
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Business trip berhasil diperbarui'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
  private function cleanCurrency($value)
    {
        return (int) preg_replace(
            '/[^\d]/',
            '',
            $value
        );
    }

}
