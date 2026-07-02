<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance\BusinessTrip\BusinessReport;
use App\Models\Attendance\BusinessTrip\BusinessReportApproval;
use App\Models\Attendance\BusinessTrip\BusinessReportAttachment;
use App\Models\Attendance\BusinessTrip\BusinessReportItem;
use App\Models\Attendance\BusinessTrip\BusinessTrip;
use App\Models\Attendance\BusinessTripAllowance;
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

class BusinessReportApiController extends Controller
{
    public function getReportableTrips()
    {
        $employee = auth()->user()->employee;

        $trips = BusinessTrip::where('employee_id', $employee->id)
            ->whereIn('status', ['approved', 'ongoing'])
            ->whereDoesntHave('report', function ($query) {
                $query->whereIn('status', ['waiting', 'approved', 'revised']);
            })
            ->with(['employee', 'costs', 'hotels', 'transportations', 'logs'])
            ->orderBy('end_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $trips
        ]);
    }

    public function getClaimApprovers()
    {
        $employee = auth()->user()->employee;
        $lineApproval = LineApproval::where('approval_type', 'Report/Claim Business Trip')->first();
        if (!$lineApproval) return response()->json(['success' => true, 'data' => []]);

        $approvers = [];
        for ($i = 1; $i <= 8; $i++) {
            $field = "approve_$i";
            if ($lineApproval->$field) {
                $emp = Employee::find($lineApproval->$field);
                if ($emp) {
                    $approvers[] = ['name' => $emp->fullname];
                }
            }
        }
        return response()->json(['success' => true, 'data' => $approvers]);
    }

    public function getMyReports()
    {
        $employee = auth()->user()->employee;
        $reports = BusinessReport::with([
            'businessTrip',
            'reportItems.attachments',
            'approvals.approver',
            'logs'
        ])
        ->where('employee_id', $employee->id)
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    public function getReportsNeedMyApproval()
    {
        $employee = auth()->user()->employee;
        $approvals = BusinessReportApproval::with([
            'businessReport.businessTrip',
            'businessReport.employee',
            'businessReport.reportItems.attachments',  // tambahkan ini
            'businessReport.approvals.approver',
            'businessReport.logs'
        ])
        ->where('approver_id', $employee->id)
        ->where('status', 'waiting')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $approvals
        ]);
    }

    public function getApprovalHistory()
    {
        $employee = auth()->user()->employee;

        $approvals = BusinessReportApproval::with(['businessReport.businessTrip', 'businessReport.employee', 'businessReport.reportItems.attachments', 'businessReport.approvals.approver', 'businessReport.logs'])
            ->where('approver_id', $employee->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // $formatted = $approvals->map(function ($approval) {
        //     $report = $approval->businessReport;
        //     return [
        //         'approval_id'       => $approval->id,
        //         'level'             => $approval->level,
        //         'report_id'         => $report->id,
        //         'no_document'       => $report->businessTrip->no_document ?? '-',
        //         'employee_name'     => $report->employee->fullname ?? '-',
        //         'trip_type'         => $report->trip_type,
        //         'start_date'        => $report->start_date,
        //         'end_date'          => $report->end_date,
        //         'arrival_to'        => $report->arrival_to,
        //         'total_cost'        => $report->total_cost,
        //         'status'            => $approval->status,
        //         'action_at'         => $approval->approved_at,
        //         'reason'            => $approval->reason,
        //     ];
        // });

        return response()->json([
            'success' => true,
            'data'    => $approvals
        ]);
    }

    public function getMealData($tripId)
    {
        $trip = BusinessTrip::with(['employee'])->findOrFail($tripId);

        if ($trip->employee_id !== auth()->user()->employee->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $attendances = EmployeeAttendance::with('detail')
            ->where('employee_id', $trip->employee_id)
            ->whereBetween('date', [$trip->start_date, $trip->end_date])
            ->get();

        $mealRows = [];
        foreach ($attendances as $attendance) {
            $detail = $attendance->detail;
            if (!$detail || !$detail->check_in || !$detail->check_out) continue;

            $hours = Carbon::parse($detail->check_in)->diffInHours(Carbon::parse($detail->check_out));
            $allowance = null;

            if ($trip->trip_type === 'domestic') {
                $allowance = BusinessTripAllowance::where('trip_type', 'domestic')
                    ->where('category', 'meal')
                    ->where('minimum_hours', '<=', $hours)
                    ->orderByDesc('minimum_hours')
                    ->first();
            } else {
                $allowance = BusinessTripAllowance::where('trip_type', 'overseas')
                    ->where('category', 'meal')
                    ->first();
            }

            if ($allowance) {
                $mealRows[] = [
                    'date'      => $attendance->date,
                    'category'  => 'meal',
                    'currency'  => $allowance->currency,
                    'amount'    => $allowance->amount,
                    'hours'     => $hours
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data'    => $mealRows
        ]);
    }

    public function submitReport(Request $request)
    {
        $request->validate([
            'business_trip_id'  => 'nullable|exists:business_trips,id',
            'trip_type'         => 'required|in:domestic,overseas',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'total_days'        => 'nullable|integer',
            'arrival_to'        => 'nullable|string',
            'purpose'           => 'required|string',
            'trip_result'       => 'nullable|string',
            'advance_amount'    => 'nullable|numeric',
            'notes'             => 'nullable|string',
            'allowances'        => 'nullable|array',
            'allowances.*.date' => 'required|date',
            'allowances.*.amount' => 'required|numeric',
            'allowances.*.currency' => 'nullable|string|size:3',
            'allowances.*.attachments' => 'nullable|array',
            'manual_expenses'   => 'nullable|array',
            'manual_expenses.*.category' => 'required|string',
            'manual_expenses.*.qty' => 'required|integer|min:1',
            'manual_expenses.*.amount' => 'required|numeric',
            'manual_expenses.*.currency' => 'nullable|string|size:3',
            'manual_expenses.*.notes' => 'nullable|string',
            'manual_expenses.*.attachments' => 'nullable|array',
        ]);

        $employee = auth()->user()->employee;

        if ($request->business_trip_id) {
            $trip = BusinessTrip::where('id', $request->business_trip_id)
                ->where('employee_id', $employee->id)
                ->whereIn('status', ['approved', 'ongoing'])
                ->first();
            if (!$trip) {
                return response()->json(['success' => false, 'message' => 'Perjalanan dinas tidak valid.'], 422);
            }
            $existing = BusinessReport::where('business_trip_id', $trip->id)
                ->whereNotIn('status', ['rejected'])->first();
            if ($existing) {
                return response()->json(['success' => false, 'message' => 'Sudah pernah membuat claim.'], 422);
            }
        }

        DB::beginTransaction();
        try {
            $report = BusinessReport::create([
                'business_trip_id'  => $request->business_trip_id,
                'employee_id'       => $employee->id,
                'level'             => $employee->level->nama ?? '-',
                'position'          => $employee->position->nama ?? '-',
                'department'        => $employee->department->name ?? '-',
                'propose_date'      => now(),
                'trip_type'         => $request->trip_type,
                'start_date'        => $request->start_date,
                'end_date'          => $request->end_date,
                'total_days'        => $request->total_days ?? 0,
                'arrival_to'        => $request->arrival_to,
                'purpose'           => $request->purpose,
                'report_result'     => $request->trip_result,
                'balance_amount'    => $request->advance_amount ?? 0,
                'currency'          => 'IDR',
                'notes'             => $request->notes,
                'status'            => 'waiting',
            ]);

            $grandTotal = 0;

            // Meal allowances
            if ($request->allowances) {
                foreach ($request->allowances as $meal) {
                    $amount = $this->cleanCurrency($meal['amount']);
                    $item = BusinessReportItem::create([
                        'business_report_id' => $report->id,
                        'category'           => 'meal',
                        'qty'                => 1,
                        'unit_amount'        => $amount,
                        'unit_total'         => $amount,
                        'currency'           => $meal['currency'] ?? 'IDR',
                        'expense_date'       => $meal['date'],
                    ]);
                    $grandTotal += $amount;
                    $this->saveAttachments($item, $meal['attachments'] ?? []);
                }
            }

            // Manual expenses
            if ($request->manual_expenses) {
                foreach ($request->manual_expenses as $exp) {
                    $amount = $this->cleanCurrency($exp['amount']);
                    $qty = (int) $exp['qty'];
                    $total = $amount * $qty;
                    $item = BusinessReportItem::create([
                        'business_report_id' => $report->id,
                        'category'           => $exp['category'],
                        'qty'                => $qty,
                        'unit_amount'        => $amount,
                        'unit_total'         => $total,
                        'currency'           => $exp['currency'] ?? 'IDR',
                        'notes'              => $exp['notes'] ?? null,
                        'expense_date'       => $report->start_date,
                    ]);
                    $grandTotal += $total;
                    $this->saveAttachments($item, $exp['attachments'] ?? []);
                }
            }

            $report->update(['total_cost' => $grandTotal]);
            if ($report->business_trip_id && $report->businessTrip) {
                $report->businessTrip->update(['status' => 'reported']);
            }

            // Approval lines
            $lineApproval = LineApproval::where('approval_type', 'Report/Claim Business Trip')->first();
            if (!$lineApproval) {
                $lineApproval = $employee->lineApprovals()->where('approval_type', 'Report/Claim Business Trip')->first();
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
                BusinessReportApproval::create([
                    'business_report_id' => $report->id,
                    'approver_id'        => $id,
                    'position'           => $approver->position->nama ?? '-',
                    'department'         => $approver->department->name ?? '-',
                    'level'              => $index + 1,
                    'status'             => $index === 0 ? 'waiting' : 'pending',
                    'approval_token'     => Str::uuid(),
                ]);
            }

            $firstApproval = $report->approvals()->where('level', 1)->first();
            if ($firstApproval && $firstApproval->approver?->user) {
                // Pastikan token tidak null
                if (empty($firstApproval->approval_token)) {
                    $firstApproval->update(['approval_token' => (string) Str::uuid()]);
                    $firstApproval->refresh();
                }
                if ($firstApproval->approval_token) {
                $details = [
                    'greeting'    => 'Hi ' . $firstApproval->approver->fullname,
                    'subject'     => 'Report / Claim Business Trip',
                    'lines'       => [
                        'Karyawan mengajukan report/claim perjalanan dinas',
                        'Nama : ' . $employee->fullname,
                        'Tipe Trip : ' . ucfirst($report->trip_type),
                        'Tujuan : ' . $report->arrival_to,
                        'Tanggal : ' . Carbon::parse($report->start_date)->format('d M Y') . ' - ' . Carbon::parse($report->end_date)->format('d M Y'),
                        'Total Claim : ' . number_format($grandTotal, 0, ',', '.'),
                    ],
                    'actionText'  => 'Lihat Pengajuan',
                    'actionURL'   => route('business-trip.approval', ['token' => $firstApproval->approval_token]) . '#pill-report-claim',
                    'thanks'      => 'Terimakasih'
                ];
                $firstApproval->approver->user->notify(new AttendancePermitNotification($details));
                }
            }

            Log::create([
                'user_id'     => Auth::id(),
                'ip_address'  => $request->ip(),
                'action'      => 'insert',
                'description' => "{$employee->fullname} mengajukan Business Report/Claim {$report->trip_type}/{$report->start_date}-{$report->end_date}"
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Claim berhasil diajukan', 'data' => ['report_id' => $report->id]], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateReport(Request $request, $id)
    {
        $report = BusinessReport::findOrFail($id);
        $employee = auth()->user()->employee;

        if ($report->employee_id !== $employee->id || $report->status !== 'revised') {
            return response()->json(['success' => false, 'message' => 'Tidak diizinkan'], 403);
        }

        $request->validate([
            'business_trip_id'  => 'nullable|exists:business_trips,id',
            'trip_type'         => 'required|in:domestic,overseas',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'total_days'        => 'nullable|integer',
            'arrival_to'        => 'nullable|string',
            'purpose'           => 'required|string',
            'trip_result'       => 'nullable|string',
            'advance_amount'    => 'nullable|numeric',
            'notes'             => 'nullable|string',
            'allowances'        => 'nullable|array',
            'manual_expenses'   => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            // Hapus item lama beserta file
            foreach ($report->reportItems as $item) {
                foreach ($item->attachments as $att) {
                    $path = storage_path('app/public/' . $att->file_path);
                    if (file_exists($path)) unlink($path);
                }
            }
            $report->reportItems()->delete();

            $report->update([
                'business_trip_id'  => $request->business_trip_id,
                'trip_type'         => $request->trip_type,
                'start_date'        => $request->start_date,
                'end_date'          => $request->end_date,
                'total_days'        => $request->total_days ?? 0,
                'arrival_to'        => $request->arrival_to,
                'purpose'           => $request->purpose,
                'report_result'     => $request->trip_result,
                'balance_amount'    => $request->advance_amount ?? 0,
                'notes'             => $request->notes,
                'status'            => 'waiting',
                'revised_count'     => ($report->revised_count ?? 0) + 1,
                'revised_level'     => null,
            ]);

            $grandTotal = 0;

            if ($request->allowances) {
                foreach ($request->allowances as $meal) {
                    $amount = $this->cleanCurrency($meal['amount']);
                    $item = BusinessReportItem::create([
                        'business_report_id' => $report->id,
                        'category'           => 'meal',
                        'qty'                => 1,
                        'unit_amount'        => $amount,
                        'unit_total'         => $amount,
                        'currency'           => $meal['currency'] ?? 'IDR',
                        'expense_date'       => $meal['date'],
                    ]);
                    $grandTotal += $amount;
                    $this->saveAttachments($item, $meal['attachments'] ?? []);
                }
            }

            if ($request->manual_expenses) {
                foreach ($request->manual_expenses as $exp) {
                    $amount = $this->cleanCurrency($exp['amount']);
                    $qty = (int) $exp['qty'];
                    $total = $amount * $qty;
                    $item = BusinessReportItem::create([
                        'business_report_id' => $report->id,
                        'category'           => $exp['category'],
                        'qty'                => $qty,
                        'unit_amount'        => $amount,
                        'unit_total'         => $total,
                        'currency'           => $exp['currency'] ?? 'IDR',
                        'notes'              => $exp['notes'] ?? null,
                        'expense_date'       => $report->start_date,
                    ]);
                    $grandTotal += $total;
                    $this->saveAttachments($item, $exp['attachments'] ?? []);
                }
            }

            $report->update(['total_cost' => $grandTotal]);

            // Reset approval
            BusinessReportApproval::where('business_report_id', $report->id)
                ->where('level', 1)->update(['status' => 'waiting', 'approved_at' => null]);
            BusinessReportApproval::where('business_report_id', $report->id)
                ->where('level', '>', 1)->update(['status' => 'pending', 'approved_at' => null]);

            $firstApproval = $report->approvals()->where('level', 1)->first();
            if ($firstApproval && $firstApproval->approval_token && $firstApproval->approver?->user) {
                $details = [
                    'greeting'    => 'Hi ' . $firstApproval->approver->fullname,
                    'subject'     => 'Report / Claim Business Trip Revised',
                    'lines'       => [
                        'Report/Claim telah direvisi oleh pemohon',
                        'Nama : ' . $employee->fullname,
                        'Tujuan : ' . $report->arrival_to,
                        'Tanggal : ' . Carbon::parse($report->start_date)->format('d M Y') . ' - ' . Carbon::parse($report->end_date)->format('d M Y'),
                        'Silakan periksa kembali.'
                    ],
                    'actionText'  => 'Lihat Pengajuan',
                    'actionURL'   => route('business-trip.approval', ['token' => $firstApproval->approval_token]) . '#pill-report-claim',
                    'thanks'      => 'Terimakasih'
                ];
                $firstApproval->approver->user->notify(new AttendancePermitNotification($details));
            }

            Log::create([
                'user_id'     => Auth::id(),
                'ip_address'  => $request->ip(),
                'action'      => 'update',
                'description' => "{$employee->fullname} mengupdate Business Report/Claim {$report->id}"
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Claim berhasil diperbarui']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function handleApproval(Request $request)
    {
        $request->validate([
            'approval_id' => 'required|exists:business_report_approvals,id',
            'action'      => 'required|in:approved,rejected,revised',
            'reason'      => 'nullable|string|required_if:action,rejected,revised',
        ]);

        $employee = auth()->user()->employee;

        DB::beginTransaction();
        try {
            $approval = BusinessReportApproval::with(['businessReport.businessTrip', 'businessReport.employee.user'])
                ->findOrFail($request->approval_id);

            if ($approval->status !== 'waiting') {
                return response()->json(['success' => false, 'message' => 'Approval sudah diproses'], 422);
            }

            $report = $approval->businessReport;
            $nextApproverEmails = [];

            if ($request->action === 'rejected') {
                $report->update(['status' => 'rejected']);
                $approval->update(['status' => 'rejected', 'reason' => $request->reason, 'approved_at' => now()]);
                \App\Models\Attendance\BusinessTrip\BusinessReportLog::create([
                    'business_report_id' => $report->id,
                    'approval_path_id'   => $approval->id,
                    'status'             => 'rejected',
                    'reason'             => $request->reason,
                    'action_at'          => now(),
                ]);
                if ($report->business_trip_id && $report->businessTrip) {
                    $report->businessTrip->update(['status' => 'ongoing']);
                }
                $this->notifyReporter($report, 'rejected', $request->reason);
            }
            elseif ($request->action === 'revised') {
                $report->update([
                    'status'        => 'revised',
                    'revised_level' => $approval->level,
                    'revised_count' => ($report->revised_count ?? 0) + 1,
                ]);
                $approval->update(['status' => 'revised', 'reason' => $request->reason]);
                \App\Models\Attendance\BusinessTrip\BusinessReportLog::create([
                    'business_report_id' => $report->id,
                    'approval_path_id'   => $approval->id,
                    'status'             => 'revised',
                    'reason'             => $request->reason,
                    'action_at'          => now(),
                ]);
                $this->notifyReporter($report, 'revised', $request->reason);
            }
            else { // approved
                $approval->update(['status' => 'approved', 'approved_at' => now()]);
                \App\Models\Attendance\BusinessTrip\BusinessReportLog::create([
                    'business_report_id' => $report->id,
                    'approval_path_id'   => $approval->id,
                    'status'             => 'approved',
                    'action_at'          => now(),
                ]);

                $nextApproval = BusinessReportApproval::where('business_report_id', $report->id)
                    ->where('level', '>', $approval->level)->orderBy('level')->first();

                if ($nextApproval) {
                    if ($nextApproval->status !== 'waiting') $nextApproval->update(['status' => 'waiting']);
 
                    if (empty($nextApproval->approval_token)) {
                        $nextApproval->update(['approval_token' => (string) Str::uuid()]);
                        $nextApproval->refresh();
                    }

                    if ($nextApproval->approval_token && $nextApproval->approver?->user?->email) {
                        $nextApproverEmails[$nextApproval->approver->user->email] = [
                            'approver_name' => $nextApproval->approver->fullname,
                            'token'         => $nextApproval->approval_token,
                        ];
                    }
                } else {
                    $report->update(['status' => 'approved', 'approved_at' => now()]);
                    if ($report->business_trip_id && $report->businessTrip) {
                        $report->businessTrip->update(['status' => 'completed']);
                    }
                    $this->notifyReporter($report, 'approved');
                }
            }

            // Kirim notifikasi ke approver berikutnya
            foreach ($nextApproverEmails as $email => $data) {
                 if (empty($data['token'])) {
                    Log::warning('Token kosong untuk approver: ' . $email);
                    continue;
                }
                $payload = [
                    'subject'    => 'Report/Claim Business Trip Menunggu Approval',
                    'greeting'   => 'Hi ' . $data['approver_name'],
                    'requests'   => [[
                        'text' => $report->employee->fullname . ' | ' . $report->arrival_to . ' | ' . number_format($report->total_cost, 0, ',', '.'),
                        'token' => $data['token']
                    ]],
                    'actionText' => 'Lihat Pengajuan',
                    'actionURL'  => route('business-trip.approval', ['token' => $data['token']]) . '#pill-report-claim',
                    'thanks'     => 'Terimakasih',
                ];
                Notification::route('mail', $email)->notify(new BulkLeaveApprovalNotification($payload));
            }

            Log::create([
                'user_id'     => Auth::id(),
                'ip_address'  => $request->ip(),
                'action'      => 'update',
                'description' => "{$employee->fullname} melakukan {$request->action} pada Report/Claim ID: {$report->id}"
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => ucfirst($request->action) . ' berhasil diproses.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $report = BusinessReport::with(['businessTrip', 'reportItems.attachments', 'approvals.approver'])
            ->findOrFail($id);

        if ($report->employee_id !== auth()->user()->employee->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json(['success' => true, 'data' => $report]);
    }

    // Helper functions
    private function saveAttachments($item, $attachments)
    {
        foreach ($attachments as $fileData) {
            $base64 = $fileData['file_base64'] ?? $fileData['base64'] ?? '';
            $fileName = $fileData['file_name'] ?? 'attachment';
            $fileContent = base64_decode(preg_replace('#^data:[\w/]+;base64,#i', '', $base64));
            $path = 'business_reports/' . date('Y/m/d');
            $fullPath = storage_path('app/public/' . $path);
            if (!file_exists($fullPath)) mkdir($fullPath, 0755, true);
            $uniqueName = time() . '_' . Str::random(8) . '_' . $fileName;
            file_put_contents($fullPath . '/' . $uniqueName, $fileContent);
            BusinessReportAttachment::create([
                'business_report_item_id' => $item->id,
                'file_name'               => $fileName,
                'file_path'               => $path . '/' . $uniqueName,
                'file_type'               => $fileData['file_type'] ?? null,
            ]);
        }
    }

    private function cleanCurrency($value)
    {
        return (int) preg_replace('/[^\d]/', '', $value);
    }

    private function notifyReporter($report, $status, $reason = null)
    {
        $user = $report->employee->user;
        if (!$user) return;

        // Ambil token approval pertama sebagai token untuk melihat detail claim
        $firstApproval = $report->approvals()->orderBy('level')->first();
        $token = $firstApproval ? $firstApproval->approval_token : null;
        $url = $token ? route('business-trip.approval', ['token' => $token]) . '#pill-report-claim' : '#';

        $subject = $status === 'approved' ? 'Claim Disetujui' : ($status === 'revised' ? 'Claim Direvisi' : 'Claim Ditolak');
        $lines = [
            $status === 'approved' ? 'Selamat, claim Anda telah disetujui.' : ($status === 'revised' ? 'Claim Anda perlu direvisi.' : 'Maaf, claim Anda ditolak.'),
            'Periode: ' . Carbon::parse($report->start_date)->format('d M Y') . ' - ' . Carbon::parse($report->end_date)->format('d M Y'),
            'Total: ' . number_format($report->total_cost, 0, ',', '.'),
        ];
        if ($reason) $lines[] = 'Catatan: ' . $reason;

        $details = [
            'greeting'    => 'Hi ' . $report->employee->fullname,
            'subject'     => $subject,
            'lines'       => $lines,
            'actionText'  => 'Lihat Detail',
            'actionURL'   => $url,
            'thanks'      => 'Terimakasih'
        ];
        $user->notify(new AttendancePermitNotification($details));
    }
}
