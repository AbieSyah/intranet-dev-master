<?php

namespace App\Http\Controllers\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\Recruitment\EmployeeRequisition;
use App\Models\Recruitment\JobPosting;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class JobPostingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = JobPosting::with(['requisition']);
            $formStatus = $request->get('form_status', 'ALL');
            if ($formStatus !== 'ALL') {
                $query->where('job_posting.status', $formStatus);
            }
            $tahun = $request->get('tahun');
            if ($tahun) {
                $query->whereYear('job_posting.created_at', $tahun);
            }
            $data = $query->get();
            return DataTables::of($data)
                ->editColumn('id', fn($data) => encrypt($data->id))
                ->editColumn('publish_id', fn($data) => $data->publish_id ?? '-')
                ->editColumn('title', fn($data) => $data->title ?? '-')
                ->addColumn('needs', fn($data) => $data->needs ?? '-')
                ->addColumn('period', function ($data) {
                    $start = optional($data->apply_start)->format('d M Y') ?? '-';
                    $end = optional($data->apply_end)->format('d M Y') ?? '-';
                    return "{$start} - {$end}";
                })
                ->addColumn('employee_status', fn($data) => $data->employee_status ?? '-')
                ->addColumn('area', fn($data) => $data->area->name ?? '-')
                ->addColumn('status', function ($data) {
                    $badges = [
                        'PUBLISH' => 'primary',
                        'DRAFT' => 'secondary',
                        'REVISE' => 'danger',
                        'DONE' => 'success',
                    ];
                    $status = $data->status;
                    if (isset($badges[$status])) {
                        $badgeClass = $badges[$status];
                        return "<span class=\"badge text-bg-{$badgeClass}\">{$status}</span>";
                    }
                    return '-';
                })
                ->addColumn('action', function ($data) {
                    $btn = '';
                    if (Auth::user()->can('hrd.recruitment.read') && ($data->status === 'DRAFT' || $data->status === 'REVISE')) {
                        $btn .= '<a href="' . route('job-posting.form', encrypt($data->id)) . '" title="Edit" class="btn btn-warning btn-sm"><i class="ri-quill-pen-line"></i></a>';
                    }
                    if (Auth::user()->can('hrd.job-posting.delete') && ($data->status != 'PUBLISH')) {
                        $btn .= '&nbsp;<a href="#" data-id="' . encrypt($data->id) . '" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-sm delete-btn"><i class="ri-delete-bin-line"></i></a>';
                    }
                    if ($data->status === 'PUBLISH') {
                        $btn .= '&nbsp;<a href="' . route('job-posting.public', encrypt($data->publish_code)) . '" title="View" target="_blank" class="btn btn-primary btn-sm"><i class="ri-external-link-line"></i></a>';
                        $btn .= '&nbsp;<a href="#" data-id="' . encrypt($data->id) . '" data-toggle="tooltip" title="Done" class="btn btn-success btn-sm done-btn"><i class="ri-check-line"></i></a>';
                        $btn .= '&nbsp;<a href="#" data-id="' . encrypt($data->id) . '" data-toggle="tooltip" title="Revise" class="btn btn-danger btn-sm revise-btn"><i class="ri-edit-2-line"></i></a>';
                    }
                    return $btn;
                })
                ->editColumn('created_at', fn($data) => $data->created_at ?? '-')
                ->rawColumns(['action', 'status'])
                ->addIndexColumn()
                ->make(true);
        }
        $years = JobPosting::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        if (empty($years)) {
            $currentYear = date('Y');
            $years = range($currentYear, $currentYear - 4); 
        }
        return view('pages.hrd.recruitment.jp.index', compact('years'));
    }

    public function form(string $id = null)
    {
        $jp = null;
        $editingRequisitionId = null;
        if ($id) {
            $id = decrypt($id);
            $jp = JobPosting::with('requisition')->findOrFail($id);
            $editingRequisitionId = $jp->requisition_id;
            if ($jp && ($jp->status === 'PUBLISH' || $jp->status === 'DONE')) {
                return redirect()->route('job-posting.index')
                    ->with('swal_warning', 'Cannot Edit Job Posting');
            }
        }
        $erQuery = EmployeeRequisition::with(['position','section','department','area'])
            ->where('status', 'DONE')
            ->where('decision', 'APPROVED')
            ->whereNotNull('no_pengajuan');
        if ($editingRequisitionId) {
            $erQuery->orWhere('id', $editingRequisitionId);
            $er = EmployeeRequisition::with(['position','section','department','area'])
                ->where('status', 'DONE')
                ->whereNotNull('no_pengajuan')
                ->where(function ($query) use ($editingRequisitionId) {
                    $query->whereDoesntHave('jobPosting') 
                        ->orWhere('id', $editingRequisitionId);
                })
                ->get();
        } else {
            $er = $erQuery->whereDoesntHave('jobPosting')->get();
        }
        return view('pages.hrd.recruitment.jp.form', compact('jp', 'er'));
    }

    public function getRequisition($requisition_id)
    {
        $er = EmployeeRequisition::with(['position', 'section', 'department', 'area'])->find($requisition_id);
        if (!$er) {
            return response()->json([]);
        }
        return response()->json([$er]); 
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $jpData = $request->except(['status','id']);
            $jpData['status'] = $request->input('status');
            $optionalIds = ['section_id','position_id','department_id','area_id','employee_status'];
            foreach ($optionalIds as $id) {
                if (empty($jpData[$id])) { 
                    $jpData[$id] = null;
                }
            }
            if (isset($jpData['apply_start'])) {
                $jpData['apply_start'] = Carbon::createFromFormat('d/m/Y', $jpData['apply_start'])->toDateString();
            }
            if (isset($jpData['apply_end'])) {
                $jpData['apply_end'] = Carbon::createFromFormat('d/m/Y', $jpData['apply_end'])->toDateString();
            }
            if (isset($jpData['title'])) {
                $jpData['title'] = strtoupper($jpData['title']);
            }
            $jp = JobPosting::updateOrCreate(['id' => $request->input('id') ?? null], $jpData);
            if ($jp->status === 'PUBLISH') {
                $title = $jp->title; 
                if (empty($title)) {
                    throw new Exception("Job Title cannot be empty when publishing.");
                }
                if (empty($jp->publish_code)) {
                    $monthYear = now()->format('mY');
                    $baseSlug = Str::slug($title);
                    $initialSlug = "{$baseSlug}-{$monthYear}";
                    $uniqueCode = Str::random(20);
                    $secureSlug = $initialSlug . '-' . $uniqueCode;
                    $publishCode = $this->makeUniqueSlug(JobPosting::class, 'publish_code', $secureSlug);
                    $jp->publish_code = $publishCode;
                }
                if (empty($jp->publish_id)) {
                    $prefix = 'JP' . now()->format('ym');
                    $lastId = JobPosting::whereNotNull('publish_id')
                        ->where('publish_id', 'like', $prefix . '%')
                        ->orderBy('publish_id', 'desc')
                        ->value('publish_id');
                    $nextNumber = 1;
                    if ($lastId) {
                        $lastNumber = (int)substr($lastId, -4);
                        $nextNumber = $lastNumber + 1;
                    }
                    $publishId = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                    $jp->publish_id = $publishId;
                }
                $jp->publish_date = now();
            }
            $jp->save();
            $user = auth()->user();
            $jobTitle = $jp->title ?? 'N/A';
            $logAction = $jp->wasRecentlyCreated ? 'insert' : 'update';
            $logDescription = ($jp->wasRecentlyCreated ? 'Create New' : 'Modify')
                . ' Job Posting [' . $jobTitle . '] with status: ' . ($jp->status ?? 'N/A');
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => $logAction,
                'description' => $logDescription,
            ]);
            DB::commit();
            return response()->json([
                'message' => "Job Posting \"$jobTitle\" has been Saved.",
                'redirect' => route('job-posting.index')
            ], 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $ids = $request->input('ids');
            $isMultiple = is_array($ids);

            if (!$isMultiple) {
                $ids = [$request->input('id')];
            }

            if (empty($ids)) {
                return redirect()->route('job-posting.index')->with('error', 'No job posting(s) were selected.');
            }

            $deletedCount = 0;
            $user = auth()->user();

            foreach ($ids as $id) {
                $jp = JobPosting::with('requisition')->findOrFail(decrypt($id));
                $jpTitle = $jp->title ?? 'N/A';
                $jpPeriod = ($jp->apply_start && $jp->apply_end) ?
                    "{$jp->apply_start->format('d M Y')} - {$jp->apply_end->format('d M Y')}" :
                    '-';
                $jpStatus = $jp->status ?? 'N/A';
                $logDescription = "Deleted Job Posting [{$jpTitle}] period {$jpPeriod} with status ({$jpStatus})";
                if (!empty($jp->publish_id)) {
                    $logDescription .= " and Publish ID: {$jp->publish_id}";
                }

                $jp->delete();

                Log::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'action' => 'delete',
                    'description' => $logDescription,
                ]);

                $deletedCount++;
            }

            return redirect()->back()->with('success', "$deletedCount job posting(s) have been successfully deleted.");
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete job posting(s): ' . $e->getMessage());
        }
    }

    protected function makeUniqueSlug(string $modelClass, string $slugColumn, string $baseSlug): string
    {
        $slug = $baseSlug;
        $i = 1;
        $separator = '-';
        while ($modelClass::where($slugColumn, $slug)->exists()) {
            $slug = $baseSlug . $separator . $i;
            $i++;
        }
        return $slug;
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'sometimes|nullable|string',
            'ids' => 'sometimes|nullable|array',
            'status' => 'required|in:REVISE,DONE',
        ]);
        $encryptedIds = $request->input('ids') ?? ($request->input('id') ? [$request->input('id')] : []);
        $newStatus = $request->input('status');
        $user = Auth::user();
        $updatedCount = 0;
        if (empty($encryptedIds)) {
            return redirect()->back()->with('error', 'No job posting were selected for status update.');
        }
        $logAction = ($newStatus === 'REVISE') ? 'revised' : 'closing';
        $successMessage = ($newStatus === 'REVISE') 
            ? 'Job Posting successfully marked for Revision!'
            : 'Job Posting successfully marked as Done!';
        DB::beginTransaction();
        try {
            foreach ($encryptedIds as $encryptedId) {
                $decryptedId = decrypt($encryptedId);
                $jobPosting = JobPosting::findOrFail($decryptedId);
                if ($jobPosting->status !== 'PUBLISH') {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Update failed. Job Posting ID: {$jobPosting->publish_id} is not currently PUBLISH.");
                }
                $jobPosting->status = $newStatus;
                $jobPosting->save();
                $logDescription = "Job Posting ID = {$jobPosting->publish_id} updated status from PUBLISH to [{$jobPosting->status}]";
                Log::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'action' => $logAction,
                    'description' => $logDescription,
                ]);
                $updatedCount++;
            }
            DB::commit();
            $finalMessage = ($updatedCount > 1) 
                ? "{$updatedCount} " . str_replace('(s)', '', $successMessage) . "s" 
                : str_replace('(s)', '', $successMessage);
            return redirect()->back()->with('success', $finalMessage);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to process request. The Job ID is invalid or the record was not found.');
        }
    }

    public function publishMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|string',
        ]);
        $encryptedIds = $request->input('ids');
        $user = Auth::user();
        $publishedCount = 0;
        DB::beginTransaction();
        try {
            foreach ($encryptedIds as $encryptedId) {
                $decryptedId = decrypt($encryptedId);
                $jp = JobPosting::findOrFail($decryptedId);
                if (empty($jp->title)) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Publish failed. Job Posting ID: {$jp->id} requires a Job Title.");
                }
                if ($jp->status === 'PUBLISH') {
                    continue; 
                }
                $jp->status = 'PUBLISH';
                if (empty($jp->publish_code)) {
                    $monthYear = now()->format('mY');
                    $baseSlug = Str::slug($jp->title);
                    $initialSlug = "{$baseSlug}-{$monthYear}";
                    $uniqueCode = Str::random(20);
                    $secureSlug = $initialSlug . '-' . $uniqueCode;
                    $publishCode = $this->makeUniqueSlug(JobPosting::class, 'publish_code', $secureSlug);
                    $jp->publish_code = $publishCode;
                }
                if (empty($jp->publish_id)) {
                    $prefix = 'JP' . now()->format('ym');
                    $lastId = JobPosting::whereNotNull('publish_id')
                        ->where('publish_id', 'like', $prefix . '%')
                        ->orderBy('publish_id', 'desc')
                        ->value('publish_id');
                    $nextNumber = 1;
                    if ($lastId) {
                        $lastNumber = (int)substr($lastId, -4);
                        $nextNumber = $lastNumber + 1;
                    }
                    $publishId = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                    $jp->publish_id = $publishId;
                }
                $jp->publish_date = now(); 
                $jp->save();
                $logDescription = "Modify Job Posting [{$jp->title}] with status: {$jp->status}";
                Log::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'action' => 'update',
                    'description' => $logDescription,
                ]);
                $publishedCount++;
            }
            DB::commit();
            $message = ($publishedCount > 1) 
                ? "{$publishedCount} Job Postings have been successfully Published."
                : 'Job Posting has been successfully Published.';
            return redirect()->back()->with('success', $message);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to process bulk publish request: ' . $e->getMessage());
        }
    }

    public function public()
    {
        return view('pages.hrd.recruitment.jp.public.detail');
    }

    public function getPublicDetail(Request $request, string $publish_code)
    {
        if ($request->ajax()) {
            try {
                $code = decrypt($publish_code); 
            } catch (DecryptException $e) {
                return response()->json(['message' => 'Invalid request code format.'], 400);
            }
            $jobPosting = JobPosting::with(['position','department','area'])
                ->where('publish_code', $code)
                ->where('status', 'PUBLISH') 
                ->first();
            if (!$jobPosting) {
                return response()->json([
                    'message' => 'Job posting not found or is no longer active.'
                ], 404);
            }
            $applyEnd = Carbon::parse($jobPosting->apply_end)->endOfDay();
            $applyStart = Carbon::parse($jobPosting->apply_start)->startOfDay();
            $isOpen = now()->between($applyStart, $applyEnd);
            $qualification_db = $jobPosting->qualification;
            $formatted_qualification = nl2br(e($qualification_db));
            $data = [
                'id' => encrypt($jobPosting->id),
                'title' => $jobPosting->title,
                'qualification' => $formatted_qualification,
                'apply_end_formatted' => $applyEnd->isoFormat('D MMMM YYYY'),
                'publish_date_formatted' => Carbon::parse($jobPosting->publish_date)->isoFormat('D MMMM YYYY'),
                'is_open' => $isOpen,
                'today_date' => now()->format('d/m/Y'),
            ];
            return response()->json(['data' => $data], 200);
        }
        abort(404);
    }
}
