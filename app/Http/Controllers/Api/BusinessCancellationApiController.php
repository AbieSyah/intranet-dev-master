<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance\BusinessTrip\BusinessCancellation;
use App\Models\Attendance\BusinessTrip\BusinessCancellationApproval;
use App\Models\Attendance\BusinessTrip\BusinessCancellationItem;
use App\Models\Attendance\BusinessTrip\BusinessTrip;
use App\Models\Attendance\BusinessTrip\BusinessCancellationLog;
use App\Models\Attendance\EmployeeAttendance;
use App\Models\Employee;
use App\Models\Log;
use App\Models\Master\LineApproval;
use App\Notifications\AttendancePermitNotification;
use App\Notifications\BulkLeaveApprovalNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Exception;


class BusinessCancellationApiController extends Controller
{
    /**
     * 1. Daftar perjalanan dinas yang dapat dibatalkan
     */
    public function getCancellableTrips()
    {
        $employee = auth()->user()->employee;

        $trips = BusinessTrip::where('employee_id', $employee->id)
            ->whereIn('status', ['cancel_waiting','approved', 'ongoing', 'draft'])
            ->whereDoesntHave('cancellation', function ($query) {
                $query->whereIn('status', ['submitted', 'approved']);
            })
            ->with(['employee', 'costs', 'hotels', 'transportations'])
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $trips
        ]);
    }

    /**
     * 2. Riwayat pembatalan milik user
     */
    public function getMyCancellations()
    {
        $employee = auth()->user()->employee;

        $cancellations = BusinessCancellation::with([
            'businessTrip.employee',
            'items',
            'approvals.approver',
            // 'logs'
        ])
        ->whereHas('businessTrip', function ($q) use ($employee) {
            $q->where('employee_id', $employee->id);
        })
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data'    => $cancellations
        ]);
    }

    /**
     * 3. Pembatalan yang perlu approval saya
     */
    public function getCancellationsNeedMyApproval()
    {
        $employee = auth()->user()->employee;

        $approvals = BusinessCancellationApproval::with([
            'businessCancellation.businessTrip.employee',
            'businessCancellation.items',
            'businessCancellation.approvals.approver',
            // 'businessCancellation.logs'
        ])
        ->where('approver_id', $employee->id)
        ->where('status', 'waiting')
        ->get();

        return response()->json([
            'success' => true,
            'data'    => $approvals
        ]);
    }

    /**
     * 4. History approval pembatalan
     */
    public function getCancellationApprovalHistory()
    {
        $employee = auth()->user()->employee;

        $approvals = BusinessCancellationApproval::with([
            'businessCancellation.businessTrip.employee',
            'businessCancellation.items',
            'businessCancellation.approvals.approver'
        ])
        ->where('approver_id', $employee->id)
        ->whereIn('status', ['approved', 'rejected'])
        ->orderBy('updated_at', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data'    => $approvals
        ]);
    }

    /**
     * 5. Submit pengajuan pembatalan
     */
    public function submitCancellation(Request $request)
    {
        $request->validate([
            'business_trip_id'        => 'required|exists:business_trips,id',
            'reason'                  => 'required|string',
            'reason_other'            => 'nullable|string',
            'employee_covered_amount' => 'nullable|numeric|min:0',
            'company_covered_amount'  => 'nullable|numeric|min:0',
            'manual_expenses'         => 'nullable|array',
            'manual_expenses.*.category' => 'required|string',
            'manual_expenses.*.qty'      => 'required|integer|min:1',
            'manual_expenses.*.amount'   => 'required|numeric|min:0',
            'manual_expenses.*.currency' => 'nullable|string|size:3',
            'manual_expenses.*.notes'    => 'nullable|string',
        ]);

        $employee = auth()->user()->employee;

        $trip = BusinessTrip::where('id', $request->business_trip_id)
            ->where('employee_id', $employee->id)
            ->whereIn('status', ['approved', 'ongoing', 'draft'])
            ->first();

        if (!$trip) {
            return response()->json([
                'success' => false,
                'message' => 'Perjalanan dinas tidak valid atau tidak dapat dibatalkan.'
            ], 422);
        }

        $existing = BusinessCancellation::where('business_trip_id', $trip->id)
            ->whereNotIn('status', ['rejected'])
            ->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Sudah pernah mengajukan pembatalan untuk perjalanan ini.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $totalLoss = 0;
            if ($request->manual_expenses) {
                foreach ($request->manual_expenses as $exp) {
                    $amount = $this->cleanCurrency($exp['amount']);
                    $qty = (int) $exp['qty'];
                    $totalLoss += $amount * $qty;
                }
            }

            $cancellation = BusinessCancellation::create([
                'business_trip_id'        => $trip->id,
                'propose_date'            => now(),
                'reason_cancel'           => $request->reason,
                'reason_other'            => $request->reason_other,
                'employee_covered_amount' => $this->cleanCurrency($request->employee_covered_amount ?? 0),
                'company_covered_amount'  => $this->cleanCurrency($request->company_covered_amount ?? 0),
                'total_loss_amount'       => $totalLoss,
                'currency'                => 'IDR',
                'status'                  => 'submitted',
            ]);

            if ($request->manual_expenses) {
                foreach ($request->manual_expenses as $exp) {
                    $amount = $this->cleanCurrency($exp['amount']);
                    $qty = (int) $exp['qty'];
                    BusinessCancellationItem::create([
                        'cancellation_id' => $cancellation->id,
                        'category'        => $exp['category'],
                        'qty'             => $qty,
                        'unit_amount'     => $amount,
                        'unit_total'      => $amount * $qty,
                        'currency'        => $exp['currency'] ?? 'IDR',
                        'notes'           => $exp['notes'] ?? null,
                    ]);
                }
            }

            // Approval lines
            $approvalType = $trip->trip_type === 'domestic' ? 'Business Trip Domestic' : 'Business Trip LuarNegeri';
            $lineApproval = LineApproval::where('approval_type', $approvalType)->first();
            if (!$lineApproval) {
                $lineApproval = $employee->lineApprovals()->where('approval_type', $approvalType)->first();
            }
            if (!$lineApproval) {
                throw new Exception('Line approval tidak ditemukan.');
            }

            $approverIds = collect([
                $lineApproval->approve_1, $lineApproval->approve_2, $lineApproval->approve_3,
                $lineApproval->approve_4, $lineApproval->approve_5, $lineApproval->approve_6,
                $lineApproval->approve_7, $lineApproval->approve_8,
            ])->filter()->values();

            foreach ($approverIds as $index => $id) {
                $approver = Employee::find($id);
                if (!$approver) continue;
                BusinessCancellationApproval::create([
                    'cancellation_id' => $cancellation->id,
                    'approver_id'     => $id,
                    'position'        => $approver->position->nama ?? '-',
                    'department'      => $approver->department->name ?? '-',
                    'level'           => $index + 1,
                    'status'          => $index === 0 ? 'waiting' : 'pending',
                    'approval_token'  => Str::uuid(),
                ]);
            }

            $firstApproval = $cancellation->approvals()->where('level', 1)->first();
            if ($firstApproval && $firstApproval->approver?->user) {
                $details = [
                    'greeting'    => 'Hi ' . $firstApproval->approver->fullname,
                    'subject'     => 'Pembatalan Perjalanan Dinas',
                    'lines'       => [
                        'Karyawan mengajukan pembatalan perjalanan dinas',
                        'Nama : ' . $employee->fullname,
                        'Trip : ' . ucfirst($trip->trip_type),
                        'Tujuan : ' . $trip->arrival_to,
                        'Tanggal : ' . Carbon::parse($trip->start_date)->format('d M Y') . ' - ' . Carbon::parse($trip->end_date)->format('d M Y'),
                        'Alasan : ' . $request->reason,
                    ],
                    'actionText'  => 'Lihat Pengajuan',
                    'actionURL'   => route('business-trip.approval', ['token' => $firstApproval->approval_token]) . '#pill-cancellation',
                    'thanks'      => 'Terimakasih'
                ];
                $firstApproval->approver->user->notify(new AttendancePermitNotification($details));
            }

            $trip->update(['status' => 'cancel_waiting']);
            $trip->approvals()->where('status', 'waiting')->update(['status' => 'pending']);

            Log::create([
                'user_id'     => Auth::id(),
                'ip_address'  => $request->ip(),
                'action'      => 'insert',
                'description' => "{$employee->fullname} mengajukan pembatalan Business Trip {$trip->no_document}"
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pembatalan berhasil diajukan',
                'data'    => ['cancellation_id' => $cancellation->id]
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 6. Proses approval (approve/reject)
     */
    public function handleApproval(Request $request)
    {
        $request->validate([
            'approval_id' => 'required|exists:business_cancellation_approvals,id',
            'action'      => 'required|in:approved,rejected',
            'reason'      => 'nullable|string|required_if:action,rejected',
        ]);

        $employee = auth()->user()->employee;

        DB::beginTransaction();
        try {
            $approval = BusinessCancellationApproval::with([
                'businessCancellation.businessTrip.employee.user',
                'approver.user'
            ])->findOrFail($request->approval_id);

            if ($approval->status !== 'waiting') {
                return response()->json([
                    'success' => false,
                    'message' => 'Approval sudah diproses sebelumnya.'
                ], 422);
            }

            $cancellation = $approval->businessCancellation;
            $businessTrip = $cancellation->businessTrip;
            $nextApproverEmails = [];

            if ($request->action === 'rejected') {
                $cancellation->update(['status' => 'rejected']);
                $approval->update([
                    'status'      => 'rejected',
                    'reason'      => $request->reason,
                    'approved_at' => now(),
                ]);
                BusinessCancellationLog::create([
                    'business_cancellation_id' => $cancellation->id,
                    'approval_path_id'         => $approval->id,
                    'status'                   => 'rejected',
                    'reason'                   => $request->reason,
                    'action_at'                => now(),
                ]);
                $newStatus = null;
                $tripApprovals = $businessTrip->approvals;
                $allApproved = $tripApprovals->every(fn($a) => $a->status === 'approved');

                if ($allApproved) {
                    $newStatus = 'approved';
                } else {
                    $anyWaitingOrPending = $tripApprovals->contains(fn($a) => in_array($a->status, ['waiting', 'pending']));
                    if ($anyWaitingOrPending) {
                        $newStatus = 'draft';
                    } else {
                        $newStatus = 'draft';
                    }
                }

                $hasCheckInOut = EmployeeAttendance::where('business_trip_id', $businessTrip->id)
                    ->where(function ($q) {
                        $q->whereNotNull('check_in')
                        ->orWhereNotNull('check_out')
                        ->orWhereHas('detail', function ($dq) {
                            $dq->whereNotNull('check_in')->orWhereNotNull('check_out');
                        });
                    })->exists();

                if ($hasCheckInOut) {
                    $newStatus = 'ongoing';
                }
                if ($newStatus) {
                    $businessTrip->update(['status' => $newStatus]);
                } else {
                    $businessTrip->update(['status' => 'draft']);
                }
                $this->notifyUser($cancellation, 'rejected', $request->reason, $approval->approval_token);
            }
            else { // approved
                $approval->update([
                    'status'      => 'approved',
                    'approved_at' => now(),
                ]);
                BusinessCancellationLog::create([
                    'business_cancellation_id' => $cancellation->id,
                    'approval_path_id'         => $approval->id,
                    'status'                   => 'approved',
                    'action_at'                => now(),
                ]);

                $nextApproval = BusinessCancellationApproval::where('cancellation_id', $cancellation->id)
                    ->where('level', '>', $approval->level)
                    ->orderBy('level')
                    ->first();

                if ($nextApproval) {
                    if ($nextApproval->status !== 'waiting') {
                        $nextApproval->update(['status' => 'waiting']);
                    }
                    if ($nextApproval->approver?->user?->email && $nextApproval->approval_token) {
                        $nextApproverEmails[$nextApproval->approver->user->email] = [
                            'approver_name' => $nextApproval->approver->fullname,
                            'token'         => $nextApproval->approval_token,
                        ];
                    }
                } else {
                    $cancellation->update(['status' => 'approved']);
                    $businessTrip->update(['status' => 'cancelled']);
                    $this->notifyUser($cancellation, 'approved', null, $approval->approval_token);
                }
            }

            foreach ($nextApproverEmails as $email => $data) {
                if (empty($data['token'])) continue;
                $payload = [
                    'subject'    => 'Pembatalan Perjalanan Dinas Menunggu Approval',
                    'greeting'   => 'Hi ' . $data['approver_name'],
                    'requests'   => [[
                        'text' => $businessTrip->employee->fullname . ' | ' . $businessTrip->arrival_to . ' | ' . Carbon::parse($businessTrip->start_date)->format('d M Y'),
                        'token' => $data['token']
                    ]],
                    'actionText' => 'Lihat Pengajuan',
                    'actionURL'  => route('business-trip.approval', ['token' => $data['token']]) . '#pill-cancellation',
                    'thanks'     => 'Terimakasih',
                ];
                Notification::route('mail', $email)->notify(new BulkLeaveApprovalNotification($payload));
            }

            Log::create([
                'user_id'     => Auth::id(),
                'ip_address'  => $request->ip(),
                'action'      => 'update',
                'description' => "{$employee->fullname} melakukan {$request->action} pada pembatalan ID: {$cancellation->id}"
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => ucfirst($request->action) . ' berhasil diproses.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function cleanCurrency($value)
    {
        return (int) preg_replace('/[^\d]/', '', $value ?? '0');
    }

    private function notifyUser($cancellation, $status, $reason = null, $token = null)
    {
        $user = $cancellation->businessTrip->employee->user;
        if (!$user) return;

        $subject = $status === 'approved' ? 'Pembatalan Disetujui' : 'Pembatalan Ditolak';
        $lines = [
            $status === 'approved' ? 'Pengajuan pembatalan perjalanan dinas Anda telah disetujui.' : 'Maaf, pengajuan pembatalan perjalanan dinas Anda ditolak.',
            'Periode: ' . Carbon::parse($cancellation->businessTrip->start_date)->format('d M Y') . ' - ' . Carbon::parse($cancellation->businessTrip->end_date)->format('d M Y'),
        ];
        if ($reason) $lines[] = 'Catatan: ' . $reason;

        // ✅ Gunakan token jika ada, jika tidak pakai '#'
        $url = $token ? route('business-trip.approval', ['token' => $token]) . '#pill-cancellation' : '#';

        $details = [
            'greeting'    => 'Hi ' . $cancellation->businessTrip->employee->fullname,
            'subject'     => $subject,
            'lines'       => $lines,
            'actionText'  => 'Lihat Detail',
            'actionURL'   => $url,
            'thanks'      => 'Terimakasih'
        ];
        $user->notify(new AttendancePermitNotification($details));
    }
}
