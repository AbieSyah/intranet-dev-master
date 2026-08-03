<?php

namespace App\Http\Controllers\ESign;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreESignRequest;
use App\Http\Requests\UpdateESignRequest;
use App\Models\Employee;
use App\Models\ESign;
use App\Models\LetterType;
use App\Services\ESignService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ESignController extends Controller
{
    public function dashboard()
    {
        $counts = [
            'total' => ESign::count(),
            'draft' => ESign::draft()->count(),
            'waiting' => ESign::waitingSign()->count(),
            'signed' => ESign::completed()->count(),
            'rejected' => ESign::rejected()->count(),
        ];

        $recentDocuments = ESign::with('employee.department', 'employee.position')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($doc) => $this->formatDocument($doc));

        return view('pages.e-sign.dashboard', compact('counts', 'recentDocuments'));
    }

    public function daftarSurat(Request $request)
    {
        $status = $request->query('status');

        $statusFilterMap = [
            'Draft' => 'draft',
            'Sign 1' => 'sign_1',
            'Sign 2' => 'sign_2',
            'Sign 3' => 'sign_3',
            'Completed' => 'completed',
            'Rejected' => 'rejected_employee',
        ];

        $jenisSurat = $request->query('jenis_surat');
        $search = $request->query('search');

        /** @var \Illuminate\Pagination\LengthAwarePaginator $documents */
        $documents = ESign::with('employee.department', 'employee.position')
            ->when($status && isset($statusFilterMap[$status]), function ($query) use ($status, $statusFilterMap) {
                return $query->where('status', $statusFilterMap[$status]);
            })
            ->when($jenisSurat, function ($query) use ($jenisSurat) {
                return $query->where('jenis_surat_slug', $jenisSurat);
            })
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nomor_surat', 'LIKE', '%' . $search . '%')
                      ->orWhereHas('employee', function ($eq) use ($search) {
                          $eq->where('fullname', 'LIKE', '%' . $search . '%')
                             ->orWhere('nik', 'LIKE', '%' . $search . '%');
                      });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        // Transform each item to array format without losing pagination
        $documents->setCollection(
            $documents->getCollection()->map(fn($doc) => $this->formatDocument($doc))
        );

        $counts = [
            'total' => ESign::count(),
            'draft' => ESign::draft()->count(),
            'waiting' => ESign::waitingSign()->count(),
            'signed' => ESign::completed()->count(),
            'rejected' => ESign::rejected()->count(),
        ];

        $currentStatus = $status;
        $letterTypes = \App\Models\LetterType::orderBy('name')->get();
        $currentJenisSurat = $jenisSurat;

        return view('pages.e-sign.daftar-surat', compact('documents', 'counts', 'currentStatus', 'letterTypes', 'currentJenisSurat', 'search'));
    }

    /**
     * Preview a saved draft document by ID.
     */
    public function preview($id)
    {
        $doc = ESign::with('employee.department', 'employee.position', 'letterType', 'template', 'creator')
            ->findOrFail($id);

        $letterType = $doc->letterType ?? LetterType::where('slug', $doc->jenis_surat_slug)->first();

        $data = [
            'slug'        => $doc->jenis_surat_slug,
            'title'       => $letterType->name ?? $doc->jenis_surat_label,
            'short_title' => $letterType->name ?? $doc->jenis_surat_label,
            'number'      => $doc->nomor_surat,
        ];

        return view('pages.e-sign.preview', compact('doc', 'data'));
    }

    /**
     * Show the form to select template and employee before creating a draft.
     *
     * Flow dari Daftar Surat → "Buat Surat Baru":
     * 1. Pilih Jenis Surat (dropdown)
     * 2. Pilih Template (terfilter berdasarkan jenis surat)
     * 3. Pilih Employee
     * 4. Klik Lanjutkan → masuk ke halaman editor
     */
    public function createSelect(Request $request)
    {
        $preselectedTypeId = $request->query('letter_type_id');

        $letterTypes = LetterType::with(['templates' => function($q) {
                $q->orderBy('version', 'desc');
            }])
            ->active()
            ->orderBy('name')
            ->get()
            ->filter(fn($t) => $t->templates->isNotEmpty());

        $employees = Employee::whereNotNull('fullname')
            ->orderBy('fullname')
            ->limit(50)
            ->get();

        // Prepare templates data as JSON for JavaScript
        $templatesData = $letterTypes->mapWithKeys(function($type) {
            return [$type->id => $type->templates->map(function($tpl) {
                return [
                    'id' => $tpl->id,
                    'title' => $tpl->title,
                    'version' => $tpl->version,
                    'is_active' => $tpl->is_active,
                    'created_at' => $tpl->created_at->diffForHumans(),
                    'content' => $tpl->content,
                ];
            })];
        });

        return view('pages.e-sign.create', compact('letterTypes', 'employees', 'templatesData', 'preselectedTypeId'));
    }

    /**
     * Show the form to create a new draft document for a specific letter type.
     * Menerima optional query param ?template_id untuk pre-select template.
     */
    public function create(Request $request, $slug)
    {
        $type = LetterType::with('activeTemplate')->where('slug', $slug)->first();

        if (!$type) {
            abort(404);
        }

        // Ambil semua template untuk jenis surat ini
        $templates = $type->templates()->orderBy('version', 'desc')->get();
        $activeTemplate = $type->activeTemplate;

        $data = [
            'slug'        => $type->slug,
            'title'       => $type->name,
            'short_title' => $type->name,
            'number'      => null,
        ];

        $employees = Employee::whereNotNull('fullname')
            ->orderBy('fullname')
            ->limit(50)
            ->get();

        // Ambil semua posisi/jabatan untuk dropdown pemilihan jabatan
        $positions = \App\Models\Position::orderBy('nama')->get();

        // Pre-select template dari query param (dari halaman create-select)
        $preselectedTemplateId = $request->query('template_id');

        $placeholders = config('esign.placeholders', []);
        $mode = 'create';
        $doc = null;

        $excludedPlaceholders = [
            'employee_name','employee_nik','employee_position','employee_department',
            'employee_birthplace','employee_birthdate','employee_gender','employee_religion',
            'employee_marital','employee_hp','employee_email',
            'employee2_name','employee2_nik','employee2_position','employee2_department',
            'employee2_birthplace','employee2_birthdate','employee2_gender','employee2_religion',
            'employee2_marital','employee2_hp','employee2_email',
            'employee3_name','employee3_nik','employee3_position','employee3_department',
            'employee3_birthplace','employee3_birthdate','employee3_gender','employee3_religion',
            'employee3_marital','employee3_hp','employee3_email',
            'nomor_surat','tanggal_mulai','tanggal_akhir','judul_surat','today',
            'sign_employee1','sign_employee2','sign_employee3',
        ];

        return view('pages.e-sign.template', compact(
            'data', 'employees', 'mode', 'doc', 'type', 'templates',
            'activeTemplate', 'placeholders', 'preselectedTemplateId',
            'excludedPlaceholders', 'positions'
        ));
    }

    /**
     * Show the form to edit an existing draft document.
     */
    public function edit(ESign $esign)
    {
        $doc = $esign->load('employee.department', 'employee.position', 'letterType', 'template');

        if (!$doc->isDraft()) {
            return redirect()
                ->route('e-sign.preview', $doc->id)
                ->with('error', 'Hanya dokumen dengan status Draft yang dapat diedit.');
        }

        $letterType = $doc->letterType ?? LetterType::where('slug', $doc->jenis_surat_slug)->first();

        // Ambil template untuk dropdown
        $templates = $letterType ? $letterType->templates()->active()->get() : collect();
        $activeTemplate = $letterType ? $letterType->activeTemplate : null;

        $data = [
            'slug'        => $doc->jenis_surat_slug,
            'title'       => $letterType->name ?? $doc->jenis_surat_label,
            'short_title' => $letterType->name ?? $doc->jenis_surat_label,
            'number'      => $doc->nomor_surat,
        ];

        $type = $letterType;

        $employees = Employee::whereNotNull('fullname')
            ->orderBy('fullname')
            ->limit(50)
            ->get();

        // Ambil semua posisi/jabatan untuk dropdown pemilihan jabatan
        $positions = \App\Models\Position::orderBy('nama')->get();

        $placeholders = config('esign.placeholders', []);
        $mode = 'edit';

        $excludedPlaceholders = [
            'employee_name','employee_nik','employee_position','employee_department',
            'employee_birthplace','employee_birthdate','employee_gender','employee_religion',
            'employee_marital','employee_hp','employee_email',
            'employee2_name','employee2_nik','employee2_position','employee2_department',
            'employee2_birthplace','employee2_birthdate','employee2_gender','employee2_religion',
            'employee2_marital','employee2_hp','employee2_email',
            'employee3_name','employee3_nik','employee3_position','employee3_department',
            'employee3_birthplace','employee3_birthdate','employee3_gender','employee3_religion',
            'employee3_marital','employee3_hp','employee3_email',
            'nomor_surat','tanggal_mulai','tanggal_akhir','judul_surat','today',
            'sign_employee1','sign_employee2','sign_employee3',
        ];

        return view('pages.e-sign.template', compact(
            'data', 'employees', 'mode', 'doc', 'type', 'templates',
            'activeTemplate', 'placeholders', 'excludedPlaceholders',
            'positions'
        ));
    }

    /**
     * Employee profile: list of e-sign documents assigned to the logged-in user.
     * Tab 'sign' = documents waiting for employee action (status: waiting_employee)
     * Tab 'done' = documents already responded to (status: approved_employee / rejected_employee)
     */
    public function profileIndex(Request $request)
    {
        $employeeId = Auth::user()->employee_id;
        $user = Auth::user();
        $tab = $request->get('tab', 'sign');

        $query = ESign::with('employee.department', 'employee.position')
            ->where(function ($q) use ($employeeId) {
                $q->where('employee1_signee_id', $employeeId)
                  ->orWhere('employee2_signee_id', $employeeId)
                  ->orWhere('employee3_signee_id', $employeeId);
            });

        if ($tab === 'sign') {
            $query->whereIn('status', [
                ESign::STATUS_SIGN_1,
                ESign::STATUS_SIGN_2,
                ESign::STATUS_SIGN_3,
            ]);
        } elseif ($tab === 'done') {
            $query->whereIn('status', [
                ESign::STATUS_COMPLETED,
                ESign::STATUS_REJECTED_EMPLOYEE,
            ]);
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(25)->appends(['tab' => $tab]);

        return view('pages.profile.e-sign.index', compact('documents', 'user', 'tab'));
    }

    /**
     * Format ESign model instance into array matching the original dummy data structure.
     * This ensures the Blade views continue to work without UI changes.
     */
    private function formatDocument($doc)
    {
        $statusDisplayMap = [
            'draft' => 'Draft',
            'sign_1' => 'Sign 1',
            'sign_2' => 'Sign 2',
            'sign_3' => 'Sign 3',
            'completed' => 'Completed',
            'rejected_employee' => 'Rejected',
        ];

        // Load signee employees
        $signee1 = $doc->employee1_signee_id ? \App\Models\Employee::find($doc->employee1_signee_id) : null;
        $signee2 = $doc->employee2_signee_id ? \App\Models\Employee::find($doc->employee2_signee_id) : null;
        $signee3 = $doc->employee3_signee_id ? \App\Models\Employee::find($doc->employee3_signee_id) : null;

        // Get template name
        $templateName = $doc->template->title ?? '-';

        return [
            'id' => $doc->id,
            'nomor_surat' => $doc->nomor_surat,
            'jenis_surat' => $doc->jenis_surat_label,
            'template_name' => $templateName,
            'nik' => $doc->employee->nik ?? '-',
            'nama' => $doc->employee->fullname ?? '-',
            'departemen' => $doc->employee->department->name ?? '-',
            'jabatan' => $doc->employee->position->nama ?? '-',
            'tanggal' => $doc->tanggal_mulai_formatted,
            'status' => $statusDisplayMap[$doc->status] ?? 'Unknown',
            'status_raw' => $doc->status,
            'signee1_name' => $signee1->fullname ?? '-',
            'signee2_name' => $signee2->fullname ?? '-',
            'signee3_name' => $signee3->fullname ?? '-',
        ];
    }

    /**
     * Store a new draft document.
     */
    public function store(StoreESignRequest $request, ESignService $esignService)
    {
        $eSign = $esignService->storeDraft($request->validated());

        return redirect()
            ->route('e-sign.preview', $eSign->id)
            ->with('success', 'Draft surat berhasil disimpan!');
    }

    /**
     * Update an existing draft document.
     */
    public function update(UpdateESignRequest $request, ESign $esign, ESignService $esignService)
    {
        $esignService->updateDraft($esign, $request->validated());

        return redirect()
            ->route('e-sign.preview', $esign->id)
            ->with('success', 'Draft surat berhasil diperbarui!');
    }

    /**
     * Send a draft document to the employee.
     */
    public function send(ESign $esign, ESignService $esignService)
    {
        $esignService->sendToEmployee($esign);

        return redirect()
            ->route('e-sign.preview', $esign->id)
            ->with('success', 'Surat berhasil dikirim ke Employee.');
    }

    /**
     * Employee approves/signs a document.
     * Mengirim employee_id yang login agar service bisa cek giliran.
     */
    public function approve(ESign $esign, ESignService $esignService)
    {
        $employeeId = Auth::user()->employee_id;
        $esignService->approveByEmployee($esign, $employeeId);

        return redirect()
            ->route('e-sign.profile-index')
            ->with('success', 'Surat berhasil ditandatangani.');
    }

    /**
     * Employee rejects a document.
     */
    public function reject(ESign $esign, ESignService $esignService)
    {
        $esignService->rejectByEmployee($esign);

        return redirect()
            ->route('e-sign.profile-index')
            ->with('success', 'Surat berhasil di-reject.');
    }

    /**
     * Generate and download PDF for a signed/approved document.
     * Uses the same Blade template as Preview (single source of truth).
     */
    public function generatePdf(ESign $esign)
    {
        $doc = $esign->load('employee.department', 'employee.position');

        $letterType = LetterType::where('slug', $doc->jenis_surat_slug)->first();

        // Gunakan gambar PNG yang sama dengan web preview (proporsi sudah pas)
        $logoPath = public_path('assets/images/KOP-terbaru.png');
        $logoData = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        $data = [
            'slug' => $doc->jenis_surat_slug,
            'title' => $letterType->name ?? $doc->jenis_surat_label,
            'short_title' => $letterType->name ?? $doc->jenis_surat_label,
            'number' => $doc->nomor_surat,
            'logo' => $logoData,
        ];

        // Ambil setting layout dari template (jika ada)
        $template = $doc->template;
        $marginTop = $template->page_margin_top ?? 25;
        $marginBottom = $template->page_margin_bottom ?? 25;
        $marginLeft = $template->page_margin_left ?? 25;
        $marginRight = $template->page_margin_right ?? 25;
        $pageSize = $template->page_size ?? 'A4';

        $filename = str_replace('/', '-', $doc->nomor_surat) . '.pdf';

        $pdf = Pdf::loadView('pages.e-sign.partials._document-pdf', compact('doc', 'data'))
            ->setPaper($pageSize, 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('isPhpEnabled', true)
            ->setOption('defaultFont', 'serif')
            ->setOption('dpi', 96)
            // Margin diatur via CSS @page
            ->setOption('marginTop', 0)
            ->setOption('marginBottom', 0)
            ->setOption('marginLeft', 0)
            ->setOption('marginRight', 0);

        return $pdf->download($filename);
    }

    /**
     * Delete a draft document.
     * Only documents with status 'draft' can be deleted.
     */
    public function destroy(ESign $esign)
    {
        if (!$esign->isDraft()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya dokumen dengan status Draft yang dapat dihapus.'
            ], 422);
        }

        $esign->delete();

        return response()->json([
            'success' => true,
            'message' => 'Draft surat berhasil dihapus.'
        ]);
    }
}
