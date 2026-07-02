<?php

namespace App\Http\Controllers\Attendance\BusinessTrip;

use App\Http\Controllers\Controller;
use App\Models\Attendance\BusinessTrip\BusinessCancellation;
use App\Models\Attendance\BusinessTrip\BusinessCancellationApproval;
use App\Models\Attendance\BusinessTrip\BusinessCancellationItem;
use App\Models\Attendance\BusinessTrip\BusinessTrip;
use App\Models\Attendance\BusinessTrip\BusinessTripCancellation;
use App\Models\Attendance\BusinessTrip\BusinessTripCancellationApproval;
use App\Models\Attendance\BusinessTrip\BusinessTripCancellationItem;
use App\Models\Employee;
use App\Models\Log;
use App\Models\Master\LineApproval;
use App\Notifications\AttendancePermitNotification;
use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CancellationController extends Controller
{
    public function create ($id)
    {
        $user = auth()->user();
        $employeeId = $user->employee->id;
        $employee = $user->employee;
        // $manualExpenses = collect();
        $user = auth()->user();

        $businessTrip = BusinessTrip::with([
            'costs',
            'transportations',
            'hotels',
        ])->findOrFail($id);
        // hanya boleh edit revised
        $manualExpenses = $businessTrip->costs
        ->whereNotIn('category', ['daily', 'laundry']);
        // $datas = BusinessTrip::where('employee_id', $employeeId)
        // // ->where('status', 'approved')
        // ->whereIn('status', ['approved','reported'])
        // ->get();
        // 🔥 cek apakah dia approver
        $isApprover = LineApproval::whereIn('approval_type', ['Business Trip Domestic','Business Trip LuarNegeri'])
        ->where(function ($q) use ($employeeId) {
            $q->where('approve_1', $employeeId)
            ->orWhere('approve_2', $employeeId)
            ->orWhere('approve_3', $employeeId)
            ->orWhere('approve_4', $employeeId)
            ->orWhere('approve_5', $employeeId)
            ->orWhere('approve_6', $employeeId)
            ->orWhere('approve_7', $employeeId)
            ->orWhere('approve_8', $employeeId);
        })
        ->exists();
        return view ('pages.profile.Attendance.business-trip.cancellation', compact('user','isApprover','manualExpenses','businessTrip'));
    }
    public function store(Request $request)
    {
        DB::beginTransaction();

        try{

            $request->validate([

                'business_trip_id' => ['required','exists:business_trips,id'],
                'reason' => 'required',
                'reason_other' => 'nullable',
                'employee_covered_amount' => 'nullable',
                'company_covered_amount' => 'nullable',
                'manual_expenses' => 'nullable|array'
            ]);
            // dd($request->all());

            $employee = auth()->user()->employee;
            $totalLoss = 0;
            if($request->manual_expenses){
                foreach($request->manual_expenses as $expense){
                    $amount = $this->cleanCurrency($expense['amount'] ?? 0);
                    $qty = intval($expense['qty'] ?? 1);
                    $totalLoss += ($amount * $qty);
                }
            }

            $cancellation = BusinessCancellation::create([
                    'business_trip_id' => $request->business_trip_id,
                    'propose_date' => now(),
                    'reason_cancel' => $request->reason,
                    'reason_other' => $request->reason_other,
                    'employee_covered_amount' => $this->cleanCurrency($request->employee_covered_amount),
                    'company_covered_amount' => $this->cleanCurrency($request->company_covered_amount),
                    'total_loss_amount' => $totalLoss,
                    'currency' => 'IDR',
                    'status' => 'draft'
                ]);

            if($request->manual_expenses){
                foreach($request->manual_expenses as $expense){
                    $amount = $this->cleanCurrency($expense['amount'] ?? 0);
                    $qty = intval($expense['qty'] ?? 1);
                    BusinessCancellationItem::create([
                        'cancellation_id' => $cancellation->id,
                        'category' => $expense['category'],
                        'qty' => $qty,
                        'unit_amount' => $amount,
                        'unit_total' => ($amount * $qty),
                        'currency' => $expense['currency']?? 'IDR',
                        'notes' => $expense['notes']
                    ]);
                }
            }

            $lineApproval = $employee->lineApprovals()->whereIn('approval_type',['Business Trip Domestic','Business Trip LuarNegeri'])->first();
            if(!$lineApproval){
                throw new Exception('Line approval Business Trip Domestic atau LuarNegeri tidak ditemukan');
            }
            $approvers = collect([
                    $lineApproval->approve_1,
                    $lineApproval->approve_2,
                    $lineApproval->approve_3,
                    $lineApproval->approve_4,
                    $lineApproval->approve_5,
                    $lineApproval->approve_6,
                    $lineApproval->approve_7,
                    $lineApproval->approve_8
                ])
                ->filter()
                ->values();
            foreach( $approvers as $index=>$id){
                $approver = Employee::with(['position','department'])->find($id);
                if(!$approver){
                    continue;
                }
                BusinessCancellationApproval::create([
                    'cancellation_id' => $cancellation->id,
                    'approver_id' => $approver->id,
                    'position' => $approver->position->nama,
                    'department' => $approver->department->name,
                    'level' => $index + 1,
                    'status' => $index === 0 ? 'waiting' : 'pending',
                    'approval_token' => Str::uuid()
                ]);
            }

            $firstApproval = BusinessCancellationApproval::with('approver.user')
                ->where('cancellation_id',$cancellation->id)
                ->where('level', 1)
                ->first();
            if($firstApproval && $firstApproval->approver?->user){
                $details = [
                    'greeting' => 'Hi '. $firstApproval->approver->fullname,
                    'subject' => 'Business Trip Cancellation',
                    'lines' => ['Terdapat pengajuan pembatalan perjalanan dinas',
                        'Nama : '. $employee->fullname,
                        'Alasan : ' . $request->reason .
                                ($request->reason_other
                                    ? ' - ' . $request->reason_other
                                    : '')
                        ],
                    'actionText' => 'Lihat Pengajuan',
                    'actionURL' => route('business-trip.approval',
                            [ 'token' => $firstApproval->approval_token]
                        ) . '#pill-cancellation',
                    'thanks' => 'Terimakasih'
                ];

                $firstApproval->approver->user->notify(new AttendancePermitNotification($details));

            }

            $businessTrip = BusinessTrip::findOrFail(
                $request->business_trip_id
            );
            $waitingApprovals = $businessTrip->approvals()
                ->with('approver.user')
                ->where('status', 'waiting')
                ->get();

            foreach ($waitingApprovals as $waitingApproval) {

                if ($waitingApproval->approver?->user) {

                    $details = [
                        'greeting' => 'Hi ' . $waitingApproval->approver->fullname,
                        'subject' => 'Business Trip Approval Dibatalkan',
                        'lines' => [
                            'Pengajuan Business Trip berikut telah diajukan pembatalan.',
                            'Nama Karyawan : ' . $employee->fullname,
                            'No Document : ' . $businessTrip->no_document,
                            'Status approval Anda saat ini berubah menjadi Pending.',
                            'Approval tidak dapat dilanjutkan sampai proses pembatalan selesai.'
                        ],
                        'actionText' => 'Lihat Detail',
                        'actionURL' => route(
                            'business-trip.approval',
                            ['token' => $waitingApproval->approval_token]
                        ),
                        'thanks' => 'Terimakasih'
                    ];

                    $waitingApproval->approver->user
                        ->notify(new AttendancePermitNotification($details));
                }
            }

            $businessTrip->approvals()
                ->where('status', 'waiting')
                ->update([
                    'status' => 'pending'
                ]);

            $businessTrip->update([
                'status' => 'cancel_waiting'
            ]);

            $user = Auth::user();
            Log::create([
                'user_id'       => $user->id,
                'ip_address'    => $request->ip(),
                'action'        => 'update',
                'description'   => "{$user->employee->fullname} Mengajukan Pembatalan Perjalanan Dinas dengan No Document {$businessTrip->no_document} {$businessTrip->trip_type } /
                                    'Alasan : ' . $request->reason .
                                            ($request->reason_other
                                                ? ' - ' . $request->reason_other
                                                : '')
                                    ],"
            ]);

            DB::commit();
            return response()->json([
                'success'=>true,
                'message'
                    =>'Pembatalan berhasil diajukan'
            ]);
        }
        catch(\Throwable $e){
            DB::rollBack();
            return response()->json([
                'success'=>false,
                'message'
                    =>$e->getMessage()
            ],500);
        }
    }

private function cleanCurrency($value)
{
    return (int)
        preg_replace(
            '/[^\d]/',
            '',
            $value ?? 0
        );
}

}
