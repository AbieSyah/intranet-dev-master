<?php

namespace App\Http\Controllers\ESign;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLetterTypeRequest;
use App\Http\Requests\StoreTemplateRequest;
use App\Http\Requests\UpdateLetterTypeRequest;
use App\Http\Requests\UpdateTemplateRequest;
use App\Services\LetterTypeService;
use App\Services\TemplateService;
use Illuminate\Http\Request;

class LetterTypeController extends Controller
{
    protected LetterTypeService $letterTypeService;
    protected TemplateService $templateService;

    public function __construct(LetterTypeService $letterTypeService, TemplateService $templateService)
    {
        $this->letterTypeService = $letterTypeService;
        $this->templateService = $templateService;
    }

    /**
     * Display all letter types.
     */
    public function index()
    {
        $letterTypes = $this->letterTypeService->index();
        return view('pages.e-sign.jenis-surat', compact('letterTypes'));
    }

    /**
     * Show form to create a new letter type.
     */
    public function create()
    {
        return view('pages.e-sign.letter-types.create');
    }

    /**
     * Store a new letter type.
     */
    public function store(StoreLetterTypeRequest $request)
    {
        $this->letterTypeService->create($request->validated());

        return redirect()
            ->route('e-sign.jenis-surat')
            ->with('success', 'Jenis surat berhasil ditambahkan!');
    }

    /**
     * Show letter type detail page (Master Jenis Surat detail with template management).
     */
    public function show(int $id)
    {
        $type = $this->letterTypeService->find($id);
        $placeholders = config('esign.placeholders', []);

        return view('pages.e-sign.letter-types.show', compact('type', 'placeholders'));
    }

    /**
     * Show form to edit letter type (with tabs: Info, Template).
     */
    public function edit(int $id)
    {
        $type = $this->letterTypeService->find($id);
        $placeholders = config('esign.placeholders', []);

        return view('pages.e-sign.letter-types.edit', compact('type', 'placeholders'));
    }

    /**
     * Update letter type.
     */
    public function update(int $id, UpdateLetterTypeRequest $request)
    {
        $this->letterTypeService->update($id, $request->validated());

        return redirect()
            ->route('e-sign.jenis-surat')
            ->with('success', 'Jenis surat berhasil diperbarui!');
    }

    /**
     * Delete letter type.
     */
    public function destroy(int $id)
    {
        try {
            $this->letterTypeService->delete($id);
            return response()->json(['success' => true, 'message' => 'Jenis surat berhasil dihapus.']);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // ========================================================================
    // TEMPLATE MANAGEMENT
    // ========================================================================

    /**
     * Store a new template for a letter type (with file upload support).
     */
    public function storeTemplate(StoreTemplateRequest $request)
    {
        $this->templateService->create($request->validated());

        return redirect()
            ->route('e-sign.jenis-surat.edit', $request->letter_type_id)
            ->with('success', 'Template berhasil ditambahkan!');
    }

    /**
     * Update a template.
     */
    public function updateTemplate(int $id, UpdateTemplateRequest $request)
    {
        $this->templateService->update($id, $request->validated());

        $template = $this->templateService->find($id);
        return redirect()
            ->route('e-sign.jenis-surat.edit', $template->letter_type_id)
            ->with('success', 'Template berhasil diperbarui!');
    }

    /**
     * Delete a template.
     */
    public function destroyTemplate(int $id)
    {
        try {
            $this->templateService->delete($id);
            return response()->json(['success' => true, 'message' => 'Template berhasil dihapus.']);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Set template as active version.
     */
    public function setActiveTemplate(int $id)
    {
        $this->templateService->setActive($id);
        $template = $this->templateService->find($id);
        return redirect()
            ->route('e-sign.jenis-surat.edit', $template->letter_type_id)
            ->with('success', 'Template aktif berhasil diperbarui.');
    }

    /**
     * Preview template (via AJAX or direct).
     */
    public function previewTemplate(int $templateId)
    {
        $rendered = $this->templateService->previewContent($templateId);
        $template = $this->templateService->find($templateId);
        $placeholders = config('esign.placeholders', []);

        if (request()->wantsJson() || request()->has('ajax')) {
            return response()->json([
                'rendered' => $rendered,
                'title' => $template->title,
                'version' => $template->version,
                'type' => $template->template_type,
                'has_file' => $template->has_file,
                'file_url' => $template->file_url,
                'file_name' => $template->file_original_name,
            ]);
        }

        return view('pages.e-sign.templates.preview', compact('template', 'rendered', 'placeholders'));
    }
}
