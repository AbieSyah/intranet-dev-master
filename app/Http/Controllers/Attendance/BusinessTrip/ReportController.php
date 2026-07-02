<?php

namespace App\Http\Controllers\Attendance\BusinessTrip;

use App\Http\Controllers\Controller;
use App\Models\Attendance\BusinessTrip\BusinessReport;
use App\Models\Attendance\BusinessTrip\BusinessReportApproval;
use App\Models\Attendance\BusinessTrip\BusinessReportAttachment;
use App\Models\Attendance\BusinessTrip\BusinessReportItem;
use App\Models\Attendance\BusinessTrip\BusinessTrip;
use App\Models\Attendance\BusinessTrip\BusinessTripReport;
use App\Models\Attendance\BusinessTrip\BusinessTripReportApproval;
use App\Models\Attendance\BusinessTrip\BusinessTripReportAttachment;
use App\Models\Attendance\BusinessTrip\BusinessTripReportItem;
use App\Models\Attendance\BusinessTripAllowance;
use App\Models\Attendance\EmployeeAttendance;
use App\Models\Employee;
use App\Models\Log;
use App\Models\Master\LineApproval;
use App\Notifications\AttendancePermitNotification;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Storage;

class ReportController extends Controller
{
    public function create ()
    {
        $user = auth()->user();
        $employeeId = $user->employee->id;
        $employee = $user->employee;
        $manualExpenses = collect();
        $mealItems = [];
        $expenseItems = [];
        $datas = BusinessTrip::where('employee_id', $employeeId)
        // ->where('status', 'approved')
        ->whereIn('status', ['approved','ongoing'])
        ->get();
        // 🔥 cek apakah dia approver
        $isApprover = LineApproval::where('approval_type', 'Report/Claim Business Trip')
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
        return view ('pages.profile.Attendance.business-trip.report-claim', compact('user','isApprover','datas','manualExpenses','mealItems','expenseItems'));
    }
    public function claimApprover(Request $request)
    {
        $user = auth()->user();

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
        ->where('approval_type', 'Report/Claim Business Trip')
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
    public function documentDetail($id)
    {
        $businessTrip = BusinessTrip::with(['costs','employee','transportations','hotels'])
        ->findOrFail($id);
        return response()->json([
            'trip'  => $businessTrip,
            'costs' => $businessTrip->costs
        ]);
    }
    public function mealData($id)
    {
        $trip = BusinessTrip::with(['employee'])
        ->findOrFail($id);

        $attendances = EmployeeAttendance::with('detail')
            ->where('employee_id', $trip->employee_id)
            ->whereBetween('date',[$trip->start_date,$trip->end_date])
            ->get();

        $mealRows = [];
        foreach($attendances as $attendance)
        {
            $hours = 0;
            $detail = $attendance->detail;
             if(
                !$detail || !$detail->check_in || !$detail->check_out)
            {
                continue;
            }
            $checkIn    = Carbon::parse($detail->check_in);
            $checkOut   = Carbon::parse($detail->check_out);
            $hours      = $checkIn->diffInHours($checkOut);
            $allowance = null;
            if($trip->trip_type === 'domestic')
            {
                $allowance =
                    BusinessTripAllowance::where(
                        'trip_type',
                        'domestic'
                    )
                    ->where(
                        'category',
                        'meal'
                    )
                    ->where(
                        'minimum_hours',
                        '<=',
                        $hours
                    )
                    ->orderByDesc(
                        'minimum_hours'
                    )
                    ->first();
            }
            else
            {
                $allowance =
                    BusinessTripAllowance::where(
                        'trip_type',
                        'overseas'
                    )
                    ->where(
                        'category',
                        'meal'
                    )
                    ->first();
            }

            if(!$allowance){
                continue;
            }
            $mealRows[] = [
                'date'      => $attendance->date,
                'category'  => 'meal',
                'currency'  => $allowance->currency,
                'amount'    => $allowance->amount,
                'hours'     => $hours

            ];
        }
        // dd($mealRows);

        return response()->json(
            $mealRows
        );
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try{

            /* ================================= VALIDATION ================================= */
            $request->validate([
                'business_trip_id'  => 'nullable|exists:business_trips,id',
                'trip_type'         => 'required',
                'start_date'        => 'required|date',
                'end_date'          => 'required|date',
                'purpose'           => 'required',
                'allowances'        => 'nullable|array',
                'manual_expenses'   => 'nullable|array'

            ]);
            // dd($request->all());
            /* ================================= REPORT ================================= */
            $employee = auth()->user()->employee;
            $businessTrip = BusinessTrip::findOrFail(
                $request->business_trip_id
            );
            if (
                Carbon::parse($businessTrip->end_date)
                    ->endOfDay()
                    ->isFuture()
            ) {

                throw new Exception(
                    'Report / Claim hanya dapat diajukan setelah perjalanan dinas selesai.'
                );

            }
            if (
                in_array(
                    $businessTrip->status,
                    ['reported', 'completed', 'cancelled','cancel_waiting']
                )
            ) {
                throw new Exception(
                    'Business Trip sudah tidak dapat diajukan report.'
                );
            }

            $report = BusinessReport::create([
                'business_trip_id'  => $request->business_trip_id,
                'employee_id'       => $employee->id,
                // SNAPSHOT
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
                'currency'          => 'IDR',
                'balance_amount'    => $request->advance_amount,
                // 'expense_method'    => $businessTrip->expense_method,
                'notes'             => $request->notes,
                'status'            => 'waiting'
            ]);
            $grandTotal = 0;
            // dd($report);
            // if($request->advance_amount !== 0){
            //     BusinessReport::create()
            // }
            /* ================================= MEAL ================================= */
            if($request->allowances){
                foreach($request->allowances as $allowance){
                    $amount = $this->cleanCurrency($allowance['amount']?? 0);
                    // default to start_date if no date provided
                    $expenseDate = $allowance['date'] ?? $report->start_date;

                    $item = BusinessReportItem::create([
                        'business_report_id'    => $report->id,
                        'category'              => 'meal',
                        'qty'                   => 1,
                        'unit_amount'           => $amount,
                        'unit_total'            => $amount,
                        'currency'              => $allowance['currency']?? 'IDR',
                        'expense_date'          => $expenseDate
                    ]);
                    $grandTotal += $amount;

                    /* ========================== ATTACHMENT ========================== */

                    if(isset($allowance['attachments'])){
                        foreach($allowance['attachments'] as $file){
                            $path = $file->store('business-report','public');

                            BusinessReportAttachment::create([
                                'business_report_item_id'  => $item->id,
                                'file_name'                     => $file->getClientOriginalName(),
                                'file_path'                     => $path,
                                'file_type'                     => $file->extension()

                            ]);
                        }
                    }
                }
            }

            /* ================================= MANUAL EXPENSE =================================*/
            if($request->manual_expenses){
                foreach($request->manual_expenses as $expense){

                    $amount = $this->cleanCurrency($expense['amount']?? 0);
                    $qty    = intval($expense['qty']?? 1);
                    $total  = $amount * $qty;
                    $item   = BusinessReportItem::create([

                        'business_report_id'    => $report->id,
                        'category'              => $expense['category'],
                        'qty'                   => $qty,
                        'unit_amount'           => $amount,
                        'unit_total'            => $total,
                        'currency'              => $expense['currency']?? 'IDR',
                        'notes'                 => $expense['notes']?? null,
                        'expense_date'          => $report->start_date
                    ]);
                    $grandTotal += $total;
                    if(isset($expense['attachments'])){
                        foreach($expense['attachments'] as $file){
                            $path = $file->store('business-report','public');

                            BusinessReportAttachment::create([
                                'business_report_item_id'  => $item->id,
                                'file_name'                => $file->getClientOriginalName(),
                                'file_path'                => $path,
                                'file_type'                => $file->extension()
                            ]);
                        }
                    }
                }
            }

            /* ================================= TOTAL ================================= */
            $report->update(['total_cost'=> $grandTotal]);
            if($report->business_trip_id && $report->businessTrip)
            {
                $report->businessTrip->update([
                    'status' => 'reported'
                ]);
            }
            /* ================================= APPROVAL ================================= */

            $employee = auth()->user()->employee;
            $lineApproval = $employee->lineApprovals()->where('approval_type', 'Report/Claim Business Trip')
                ->first();

            if(!$lineApproval){
                throw new Exception(
                    'Line approval tidak ditemukan'
                );

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
            ])->filter()->values();
            foreach($approvers as $index=>$id){
                $approver = Employee::with([
                        'position',
                        'department'
                    ])->find($id);

                    if (!$approver) {
                        continue;
                    }
                BusinessReportApproval::create([
                    'business_report_id'    => $report->id,
                    'approver_id'           => $id,
                    'position'              => $approver->position->nama,
                    'department'            => $approver->department->name,
                    'level'                 => $index+1,
                    'status'                => $index===0 ? 'waiting': 'pending',
                    'approval_token'        => Str::uuid()

                ]);
            }
            $firstApproval = BusinessReportApproval::with([
                    'approver.user'
                ])
                ->where('business_report_id', $report->id)
                ->where('level', 1)
                ->first();

                if ($firstApproval && $firstApproval->approver?->user) {
                    $details = [
                        'greeting'  => 'Hi ' . $firstApproval->approver->fullname,
                        'subject'   => 'Report / Pengajuan Claim Business Trip',
                        'lines'     => [
                            'Karyawan Melakukan Report / mengajukan Claim perjalanan dinas',
                            'Nama : ' . $employee->fullname,
                            'Tipe Trip : ' . ucfirst($report->trip_type),
                            'Tujuan : ' . $report->arrival_to,
                            'Tanggal : ' .
                                Carbon::parse($report->start_date)
                                    ->format('d M Y')
                                . ' - ' .
                                Carbon::parse($report->end_date)
                                    ->format('d M Y'),
                            // 'Nomor Dokumen : ' .
                            //     $report->business_trip_id->no_document,
                        ],
                        'actionText'=> 'Lihat Pengajuan',

                        'actionURL' => route('business-trip.approval',
                            [
                                'token' => $firstApproval->approval_token
                            ]
                        ) . '#pill-report-claim',

                        'thanks' => 'Terimakasih'
                    ];

                    $firstApproval->approver->user
                        ->notify(
                            new AttendancePermitNotification($details)
                        );
                }

            $user = Auth::user();
            Log::create([
                'user_id'       => $user->id,
                'ip_address'    => $request->ip(),
                'action'        => 'update',
                'description'   => "{$user->employee->fullname} Mengajukan Business Report/Claim {$report->trip_type }/{$report->start_date}-{$report->end_date}"
            ]);

            DB::commit();
            return response()->json([
                'success'   =>true,
                'message'   =>'Report berhasil dibuat'
            ]);
        }
        catch(\Throwable $e){
            DB::rollBack();
            return response()->json([
                'success'   =>false,
                'message'   =>$e->getMessage()
            ],500);
        }
    }
    public function edit($id)
    {
        $user = auth()->user();
        $id = decrypt($id);
        $businessReport = BusinessReport::with([
                'businessTrip',
                'reportItems.attachments',
                'approvals'
            ])
            ->where('employee_id',$user->employee->id)
            ->findOrFail($id);
        // hanya revised boleh edit
        if($businessReport->status!== 'revised'){
            abort(403);
        }
        // dropdown document
        $datas = BusinessTrip::where('employee_id',$user->employee->id)
            ->orderByDesc('id')
            ->get();
        // meal & expense dipisah
        $mealItems = $businessReport->reportItems->where('category','meal')->values();
        $expenseItems = $businessReport->reportItems->where('category','!=','meal')
                ->values();
        return view('pages.profile.Attendance.business-trip.report-claim',compact(
                'businessReport',
                'user',
                'datas',
                'mealItems',
                'expenseItems'
            )
        );
    }
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            $id = decrypt($id);

            $report = BusinessReport::with([
                'reportItems.attachments',
                'approvals',
                'businessTrip',
                'employee'
            ])->findOrFail($id);

            if($report->status !== 'revised'){
                throw new Exception(
                    'Report tidak dapat diubah'
                );
            }

            $request->validate([
                'business_trip_id' => 'nullable|exists:business_trips,id',
                'trip_type' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'purpose' => 'required'
            ]);

            $oldMealAttachments = [];
            $oldExpenseAttachments = [];

            foreach($report->reportItems as $item){
                $attachments =
                    $item->attachments
                        ->map(function($file){
                            return [
                                'id' => $file->id,
                                'file_name'=>$file->file_name,
                                'file_path'=>$file->file_path,
                                'file_type'=>$file->file_type
                            ];
                        })
                        ->toArray();
                if($item->category === 'meal'){
                    $oldMealAttachments[$item->id] = $attachments;
                }else{
                    $oldExpenseAttachments[$item->id] = $attachments;
                }
            }

            $report->update([
                'business_trip_id'=>$request->business_trip_id,
                'trip_type'=>$request->trip_type,
                'start_date'=>$request->start_date,
                'end_date'=>$request->end_date,
                'total_days'=>$request->total_days ?? 0,
                'arrival_to'=>$request->arrival_to,
                'purpose'=>$request->purpose,
                'report_result'=>$request->trip_result,
                'balance_amount'=>$request->advance_amount,
                'notes'=>$request->notes,
                'status'=>'waiting',
                'revised_count'=>($report->revised_count ?? 0)+1
            ]);
            // dd($request->all());

            $deleted = $request->deleted_existing_files ?? [];
            if(!is_array($deleted)) $deleted = [$deleted];
            $deleted = array_map('intval', $deleted);

            foreach($report->reportItems as $item){
                $item->attachments()->delete();
            }
            $report->reportItems()->delete();
            $grandTotal = 0;

            if($request->allowances){
                foreach($request->allowances as $index=>$allowance){
                    $amount = $this->cleanCurrency($allowance['amount'] ?? 0);
                    $item = BusinessReportItem::create([
                            'business_report_id'=>$report->id,
                            'category'=>'meal',
                            'qty'=>1,
                            'unit_amount'=>$amount,
                            'unit_total'=>$amount,
                            'currency'=>$allowance['currency'] ?? 'IDR',
                            'expense_date'=>$allowance['date']

                        ]);

                    $grandTotal += $amount;

                    if(!empty($allowance['item_id']) && isset($oldMealAttachments[$allowance['item_id']] )){
                        foreach($oldMealAttachments[$allowance['item_id']] as $file){
                            if(in_array($file['id'], $deleted)){
                                continue;
                            }
                            BusinessReportAttachment::create([
                                'business_report_item_id'=>$item->id,
                                'file_name'=>$file['file_name'],
                                'file_path'=>$file['file_path'],
                                'file_type'=>$file['file_type']
                            ]);
                        }
                    }

                    if(isset($allowance['attachments'])){

                        foreach(
                            $allowance['attachments']
                            as $file
                        ){

                            $path =
                                $file->store(
                                    'business-report',
                                    'public'
                                );

                            BusinessReportAttachment::create([

                                'business_report_item_id'=>$item->id,
                                'file_name'=>$file->getClientOriginalName(),
                                'file_path'=>$path,
                                'file_type'=>$file->extension()

                            ]);
                        }
                    }
                }
            }

            if($request->manual_expenses){
                foreach($request->manual_expenses as $index=>$expense){
                    $amount = $this->cleanCurrency($expense['amount'] ?? 0);
                    $qty = intval($expense['qty'] ?? 1);
                    $total = $amount * $qty;
                    $item = BusinessReportItem::create([
                            'business_report_id'=>$report->id,
                            'category'=>$expense['category'],
                            'qty'=>$qty,
                            'unit_amount'=>$amount,
                            'unit_total'=>$total,
                            'currency'=>$expense['currency'] ?? 'IDR',
                            'notes'=>$expense['notes'],
                            'expense_date'=>$report->start_date
                        ]);
                    $grandTotal += $total;
                    if(!empty($expense['item_id']) && isset($oldExpenseAttachments[$expense['item_id']])){
                        foreach($oldExpenseAttachments[$expense['item_id']] as $file){
                            if(in_array($file['id'], $deleted)){
                                continue;
                            }
                            BusinessReportAttachment::create([
                                'business_report_item_id'=>$item->id,
                                'file_name'=>$file['file_name'],
                                'file_path'=>$file['file_path'],
                                'file_type'=>$file['file_type']
                            ]);
                        }
                    }
                    if(isset($expense['attachments'])){
                        foreach($expense['attachments'] as $file){
                            $path = $file->store('business-report','public');
                            BusinessReportAttachment::create([
                                'business_report_item_id'=>$item->id,
                                'file_name'=>$file->getClientOriginalName(),
                                'file_path'=>$path,
                                'file_type'=>$file->extension()
                            ]);
                        }
                    }
                }
            }
            $report->update(['total_cost'=>$grandTotal]);

            BusinessReportApproval::where('business_report_id',$report->id)
            ->update([
                'approved_at' => null
            ]);

            BusinessReportApproval::where('business_report_id',$report->id)
            ->where('level', 1)
            ->update([
                'status' => 'waiting'
            ]);

            BusinessReportApproval::where('business_report_id', $report->id)
            ->where('level', '>', 1)
            ->update([
                'status' => 'pending'
            ]);
            $report->update([
                'status' => 'waiting',
                'revised_level' => null
            ]);

            $firstApproval = BusinessReportApproval::with(['approver.user'])
                ->where('business_report_id',$report->id)
                ->where('level', 1)
                ->first();
            if($firstApproval && $firstApproval->approver?->user){
                $details = [
                    'greeting' => 'Hi '.$firstApproval->approver->fullname,
                    'subject' => 'Report / Claim Business Trip Revised',
                    'lines' => [
                        'Report / Claim Business Trip telah direvisi',
                        'Nama : '. $report->employee->fullname,
                        'Trip Type : '. ucfirst($report->trip_type),
                        'Tujuan : '. $report->arrival_to,
                        'Tanggal : '. Carbon::parse($report->start_date)->format('d M Y')
                            .' - '.
                            Carbon::parse($report->end_date)->format('d M Y'),
                    ],
                    'actionText' => 'Lihat Pengajuan',
                    'actionURL' => route('business-trip.approval',['token'=>$firstApproval->approval_token]).'#pill-report-claim',
                    'thanks' => 'Terimakasih'

                ];

                $firstApproval->approver->user->notify(new AttendancePermitNotification($details));
            }

            // ================= LOG =================
            $user = Auth::user();
            Log::create([
                'user_id'       => $user->id,
                'ip_address'    => $request->ip(),
                'action'        => 'update',
                'description'   => "{$user->employee->fullname} update Business Report/Claim {$report->trip_type}/{$report->start_date}-{$report->end_date}"
            ]);

            DB::commit();

            return response()->json([
                'success'=>true,
                'message'=>'Report berhasil diperbarui'
            ]);
        }
        catch(\Throwable $e){
            DB::rollBack();
            return response()->json([
                'success'=>false,
                'message'=>$e->getMessage()
            ],500);
        }
    }

    private function cleanCurrency($value){
        return (int) preg_replace(
            '/[^\d]/',
            '',
            $value
        );
    }

}
