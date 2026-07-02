<?php

namespace App\Http\Controllers\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\Recruitment\Candidate;
use App\Models\Recruitment\JobPosting;
use App\Notifications\RecruitmentNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CandidateController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $postingId = $request->get('posting_id');
            $query = Candidate::with(['posting','experiences','educations'])->withCount('selections');
            if ($postingId && $postingId !== 'ALL') {
                $query->where('posting_id', $postingId);
            }
            return DataTables::of($query)
                ->editColumn('id', function ($row) {
                    $encryptedId = encrypt($row->id);
                    $isSafeToDelete = ($row->selections_count === 0);
                    $isDisabled = ($isSafeToDelete && Auth::user()->can('hrd.candidate.delete')) ? '' : 'disabled';
                    return '<input type="checkbox" class="row-checkbox" value="' . $encryptedId . '" ' . $isDisabled . '>';
                })
                ->addColumn('job', function ($row) {
                    return optional($row->posting)->title ?? '-';
                })
                ->editColumn('fullname', function ($row) {
                    return $row->fullname ?? '-';
                })
                ->addColumn('age', function ($row) {
                    $birthDate = Carbon::parse($row->birthdate);
                    return $birthDate->diff(Carbon::now())->format('%y Years');
                })
                ->addColumn('edu', function ($row) {
                    $educations = $row->educations->sortBy('id'); 
                    $count = $educations->count();
                    $output = '';
                    if ($count === 1) {
                        $education = $educations->first();
                        $institution = $education->institution_name;
                        return (!empty($institution) && $institution !== '-') ? $institution : '-';
                    }
                    foreach ($educations as $index => $education) {
                        $nomor = $index + 1; 
                        $institution = $education->institution_name;
                        $displayEdu = (!empty($institution) && $institution !== '-') ? $institution : '-';
                        $output .= "{$nomor}. {$displayEdu}";
                        if ($index < $count - 1) { 
                            $output .= '<br>'; 
                        }
                    }
                    return $output ?: '-';
                })
                ->addColumn('years_exp', function ($row) {
                    $experiences = $row->experiences->sortBy('id');
                    $count = $experiences->count();
                    $output = '';
                    if ($count === 1) {
                        $experience = $experiences->first();
                        $year = $experience->years;
                        return (!empty($year) && $year !== '-') ? "{$year} Years" : '-';
                    }
                    foreach ($experiences as $index => $experience) {
                        $nomor = $index + 1;
                        $year = $experience->years;
                        $displayYear = (!empty($year) && $year !== '-') ? "{$year} Years" : '-';
                        $output .= "{$nomor}. {$displayYear}";
                        if ($index < $count - 1) {
                            $output .= '<br>';
                        }
                    }
                    return $output ?: '-'; 
                })
                ->addColumn('position', function ($row) {
                    $experiences = $row->experiences->sortBy('id'); 
                    $count = $experiences->count();
                    $output = '';
                    if ($count === 1) {
                        $experience = $experiences->first();
                        $position = $experience->position;
                        return (!empty($position) && $position !== '-') ? $position : '-';
                    }
                    foreach ($experiences as $index => $experience) {
                        $nomor = $index + 1; 
                        $position = $experience->position;
                        $displayPosition = (!empty($position) && $position !== '-') ? $position : '-';    
                        $output .= "{$nomor}. {$displayPosition}";
                        if ($index < $count - 1) { 
                            $output .= '<br>'; 
                        }
                    }
                    return $output ?: '-';
                })
                ->addColumn('company', function ($row) {
                    $experiences = $row->experiences->sortBy('id');
                    $count = $experiences->count();
                    $output = '';
                    if ($count === 1) {
                        $experience = $experiences->first();
                        $company = $experience->company;
                        return (!empty($company) && $company !== '-') ? $company : '-';
                    }
                    foreach ($experiences as $index => $experience) {
                        $nomor = $index + 1;
                        $company = $experience->company;
                        $displayCompany = (!empty($company) && $company !== '-') ? $company : '-';   
                        $output .= "{$nomor}. {$displayCompany}";
                        if ($index < $count - 1) { 
                            $output .= '<br>'; 
                        }
                    }
                    return $output ?: '-';
                })
                ->addColumn('skill', function ($row) {
                    return $row->skill ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $encryptedId = encrypt($row->id);
                    $btn .= '<a href="' . route('candidate.detail', ['id' => $encryptedId]) . '" title="Detail" class="btn btn-info btn-sm me-1"><i class="ri-eye-2-line"></i></a>';
                    $isSafeToDelete = ($row->selections_count === 0);
                    if (Auth::user()->can('hrd.candidate.delete') && $isSafeToDelete) {
                        $btn .= '<button type="button" data-id="' . $encryptedId . '" title="Delete" class="btn btn-danger btn-sm delete-btn me-1"><i class="ri-delete-bin-line"></i></button>';
                    }
                    if ($row->selections_count > 0) {
                        $btn .= '<a href="#" data-id="' . $encryptedId . '" title="Steps" class="btn btn-primary btn-sm me-1 btn-view-steps"><i class="ri-list-check"></i></a>';
                    }
                    return $btn;
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at;
                })
                ->rawColumns(['id', 'action', 'years_exp', 'position', 'company', 'edu', 'status'])
                ->make(true);
        }
        $postings = JobPosting::select('id','title','publish_id')
                        ->whereHas('candidates') 
                        ->orderBy('title', 'asc')
                        ->get();
        return view('pages.hrd.recruitment.candidate.index', compact('postings'));
    }

    public function destroy(Request $request)
    {
        $ids = $request->input('ids');
        if (!is_array($ids)) {
            $ids = [$request->input('id')];
        }
        $decryptedIds = array_map(function ($id) {
            try {
                return decrypt($id);
            } catch (DecryptException $e) {
                return null;
            }
        }, $ids);
        $decryptedIds = array_filter($decryptedIds);
        if (empty($decryptedIds)) {
            return redirect()->back()->with('error', 'No valid Candidate(s) were selected.');
        }
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $allCandidates = Candidate::with('selections')->whereIn('id', $decryptedIds)->get();
            $candidatesToKeep = [];
            $candidatesToDelete = [];
            foreach ($allCandidates as $candidate) {
                if ($candidate->selections->isNotEmpty()) {
                    $candidatesToKeep[] = $candidate->fullname;
                } else {
                    $candidatesToDelete[] = $candidate->id;
                }
            }
            if (empty($candidatesToDelete)) {
                $message = !empty($candidatesToKeep) 
                    ? 'Deletion failed. Candidate(s) cannot be deleted because they are Selection Process.'
                    : 'No Candidate(s) were found for deletion.';
                DB::rollback();
                return redirect()->back()->with('error', $message);
            }
            $candidatesForDeletion = Candidate::with(['posting', 'educations'])
                ->whereIn('id', $candidatesToDelete)
                ->get();   
            $deletedCount = 0;
            $filesToDelete = [];
            foreach ($candidatesForDeletion as $candidate) {
                $candidateName = $candidate->fullname ?? 'N/A';
                $candidateID = $candidate->no_ktp ?? 'N/A';
                $posting = $candidate->posting->title ?? 'N/A';
                $logDescription = "Deleted Candidate Name : {$candidateName} [{$candidateID}] from Job Posting : {$posting}";
                Log::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'action' => 'delete',
                    'description' => $logDescription,
                ]);
                if ($candidate->photo) {
                    $filesToDelete[] = 'candidates/photos/' . $candidate->photo;
                }
                foreach ($candidate->educations as $education) {
                    if ($education->ijazah) {
                        $filesToDelete[] = 'candidates/ijazah/' . $education->ijazah;
                    }
                }
                $candidate->delete();
                $deletedCount++;
            }
            if (!empty($filesToDelete)) {
                Storage::disk('public')->delete($filesToDelete);
            }
            DB::commit();
            $successMessage = "$deletedCount Candidate(s) have been successfully deleted.";
            if (!empty($candidatesToKeep)) {
                $namesKept = implode(', ', $candidatesToKeep);
                $successMessage .= " Note: Candidate(s) {$namesKept} were skipped because they are already ACCEPTED.";
            }
            return redirect()->back()->with('success', $successMessage);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to delete Candidate(s): ' . $e->getMessage());
        }
    }

    public function detail($id)
    {
        $user = auth()->user();
        $id = decrypt($id);
        $c = Candidate::with(['posting','experiences','educations'])->findOrFail($id);
        return view('pages.hrd.recruitment.candidate.detail', compact(
            'user',
            'c',
        ));
    }

    public function publicStore(Request $request)
    {
        $rules = [
            'job_id' => ['required', 'string'],
            'no_ktp' => ['required', 'string', 'max:50'],
            'fullname' => ['required', 'string', 'max:255'],
            'nickname' => ['required', 'string', 'max:255'],
            'ktp_address' => ['required', 'string', 'max:255'],
            'domicile_address' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'birthplace' => ['required', 'string', 'max:100'],
            'birthdate' => ['required', 'date_format:d/m/Y', 'before_or_equal:today'],
            'gender' => ['required', 'in:Male,Female'],
            'religion' => ['required', 'string', 'max:50'],
            'marital' => ['required', 'string', 'max:50'],
            'height' => ['required', 'integer', 'min:50', 'max:300'],
            'weight' => ['required', 'integer', 'min:20', 'max:500'],
            'skill' => ['required', 'string'],
            'expected_salary' => ['required', 'string'],
            'is_negotiable'   => ['nullable'],
            'photo' => ['required', 'string'],
            'traffic_source' => ['nullable', 'string'],
            
            // Education
            'educations' => ['required', 'array'], 
            'educations.*.level' => ['required', 'string', 'max:50'],
            'educations.*.institution_name' => ['required', 'string', 'max:255'],
            'educations.*.major' => ['required', 'string', 'max:100'],
            'educations.*.year_graduated' => ['required', 'digits:4', 'integer', 'max:' . now()->year],
            'educations.*.score_gpa' => ['required', 'numeric', 'min:0', 'max:999.99'],
            'educations.*.ijazah' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'], 
            
            // Experience
            'experiences' => ['nullable', 'array'], 
            'experiences.*.company' => ['nullable', 'string', 'max:255'],
            'experiences.*.position' => ['nullable', 'string', 'max:255'],
            'experiences.*.years' => ['nullable', 'integer', 'min:0', 'max:100'],

            // CloudFlare
            'cf-turnstile-response' => ['required', 'string'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi Gagal. Mohon periksa kembali isian formulir Anda.',
                'errors' => $validator->errors()
            ], 422);
        }
        $cfResponseToken = $request->input('cf-turnstile-response');
        $secretKey = env('CLOUDFLARE_TURNSTILE_SECRET_KEY');
        if (!$secretKey) {
            return response()->json(['message' => 'Terjadi kesalahan internal. (TS-01)'], 500);
        }
        $verificationResponse = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => $secretKey, 
            'response' => $cfResponseToken,
            'remoteip' => $request->ip(),
        ]);
        $verificationData = $verificationResponse->json();
        if (!($verificationData['success'] ?? false)) {
            $errorCodes = $verificationData['error-codes'] ?? ['verification_failed'];
            return response()->json([
                'message' => 'Verifikasi keamanan gagal. Mohon coba lagi atau refresh halaman.',
                'errors' => ['cf-turnstile-response' => ['Verifikasi keamanan gagal: ' . implode(', ', $errorCodes)]]
            ], 422);
        }
        try {
            $postingId = decrypt($request->job_id);
        } catch (DecryptException $e) {
            return response()->json(['message' => 'Pekerjaan Tidak Valid.'], 400);
        }
        $jobPosting = JobPosting::findOrFail($postingId);
        if (!$jobPosting || $jobPosting->status !== 'PUBLISH' || Carbon::parse($jobPosting->apply_end)->lt(now())) {
            return response()->json(['message' => 'Lowongan ini Sudah Ditutup'], 403);
        }
        $dataToStore = $validator->validated();
        if (isset($dataToStore['cf-turnstile-response'])) {
            unset($dataToStore['cf-turnstile-response']);
        }
        if ($request->has('is_negotiable') || isset($dataToStore['is_negotiable'])) {
            $dataToStore['expected_salary'] = $dataToStore['expected_salary'] . ' (Negotiable)';
        }
        unset($dataToStore['is_negotiable']);
        $experiencesData = $dataToStore['experiences'] ?? [];
        $educationsData = $dataToStore['educations'] ?? [];
        $base64Image = $dataToStore['photo'];
        unset($dataToStore['experiences'], $dataToStore['educations'], $dataToStore['job_id']);
        unset($dataToStore['photo']);
        $fieldsToUppercase = [
            'fullname', 
            'nickname', 
            'ktp_address', 
            'domicile_address', 
            'birthplace'
        ];
        foreach ($fieldsToUppercase as $field) {
            if (isset($dataToStore[$field])) {
                $dataToStore[$field] = strtoupper($dataToStore[$field]);
            }
        }
        $dataToStore['birthdate'] = Carbon::createFromFormat('d/m/Y', $dataToStore['birthdate'])->format('Y-m-d');
        $dataToStore['posting_id'] = $postingId;
        $dataToStore['submit_date'] = now();
        $dataToStore['position_id'] = $jobPosting->position_id ?? null;
        $dataToStore['department_id'] = $jobPosting->department_id ?? null;
        $dataToStore['section_id'] = $jobPosting->section_id ?? null;
        $dataToStore['area_id'] = $jobPosting->area_id ?? null;
        $positionName = $jobPosting->title ?? 'Posisi Tidak Diketahui';
        // Log
        $dataToStore['ip_address'] = $request->ip();
        $dataToStore['user_agent'] = $request->header('User-Agent');
        $dataToStore['referer_source'] = $request->input('traffic_source') ?? 'Direct Access';
        $dataToStore['captcha_verified_at'] = now();
        
        DB::beginTransaction();
        $storedPhotoPath = null;
        $storedIjazahPaths = [];
        try {
            if ($base64Image) {
                if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                    $dataType = strtolower($type[1]); 
                    $data = substr($base64Image, strpos($base64Image, ',') + 1);
                    $data = base64_decode($data);
                    $filename = time() . '_' . uniqid() . '.' . $dataType;
                    $path = 'candidates/photos/' . $filename;
                    Storage::disk('public')->put($path, $data); 
                    $dataToStore['photo'] = $filename;
                    $storedPhotoPath = $path;
                } else {
                    throw new Exception('Format foto Base64 tidak valid.');
                }
            }
            // Create Candidate
            $candidate = Candidate::create($dataToStore);
            // Save Education with File
            $educationRecords = [];
            if (!empty($educationsData)) {
                foreach ($educationsData as $edu) {
                    $ijazahFile = $edu['ijazah'];
                    $ijazahFilename = $candidate->no_ktp . '_edu_' . $edu['year_graduated'] . '_' . uniqid() . '.' . $ijazahFile->getClientOriginalExtension();
                    $ijazahPath = $ijazahFile->storeAs('candidates/ijazah', $ijazahFilename, 'public'); 
                    $storedIjazahPaths[] = $ijazahPath;
                    $educationRecords[] = [
                        'level' => $edu['level'],
                        'institution_name' => $edu['institution_name'],
                        'major' => $edu['major'],
                        'year_graduated' => $edu['year_graduated'],
                        'score_gpa' => $edu['score_gpa'],
                        'ijazah' => $ijazahFilename, 
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                $candidate->educations()->createMany($educationRecords);
            }
            // Save Experience
            if (!empty($experiencesData)) {
                $filteredExperiences = array_filter($experiencesData, function($exp) {
                    return !empty($exp['company']) || !empty($exp['position']) || !empty($exp['years']);
                });
                $candidate->experiences()->createMany($filteredExperiences);
            }
            DB::commit();
            $candidate->notify(new RecruitmentNotification(
                $dataToStore,
                $positionName,
                'submit'
            ));
            return response()->json([
                'message' => 'Lamaran Anda berhasil dikirim! Kami akan menghubungi Anda jika lolos ke tahap selanjutnya.'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            if ($storedPhotoPath) {
                Storage::disk('public')->delete($storedPhotoPath);
            }
            if (!empty($storedIjazahPaths)) {
                Storage::disk('public')->delete($storedIjazahPaths);
            }
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data. Mohon coba lagi.',
                'errors' => ['server' => ['Terjadi kesalahan internal.']]
            ], 500);
        }
    }
}
