<?php

namespace App\Http\Controllers\ESign;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTemplateRequest;
use App\Http\Requests\UpdateTemplateRequest;
use App\Models\LetterType;
use App\Services\TemplateService;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    protected TemplateService $templateService;

    public function __construct(TemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    /**
     * Display all templates, optionally filtered by letter_type_id.
     */
    public function index(Request $request)
    {
        $letterTypeId = $request->query('letter_type_id');

        if (!$letterTypeId) {
            return redirect()->route('e-sign.jenis-surat');
        }

        $search = $request->query('search');

        $templates = $this->templateService->index($letterTypeId, $search);
        $letterTypes = LetterType::orderBy('name')->get();

        $selectedType = null;
        if ($letterTypeId) {
            $selectedType = LetterType::find($letterTypeId);
        }

        return view('pages.e-sign.templates.index', compact('templates', 'letterTypes', 'selectedType', 'letterTypeId', 'search'));
    }

    /**
     * Show form to create a new template.
     */
    public function create(Request $request)
    {
        $letterTypes = LetterType::orderBy('name')->get();
        $preselectedTypeId = $request->query('letter_type_id');

        return view('pages.e-sign.templates.create', compact('letterTypes', 'preselectedTypeId'));
    }

    /**
     * Store a new template.
     */
    public function store(StoreTemplateRequest $request)
    {
        $template = $this->templateService->create($request->validated());

        return redirect()
            ->route('e-sign.jenis-surat.edit', $template->letter_type_id)
            ->with('success', 'Template berhasil ditambahkan!');
    }

    /**
     * Show form to edit a template.
     */
    public function edit(int $id)
    {
        $template = $this->templateService->find($id);

        return view('pages.e-sign.templates.edit', compact('template'));
    }

    /**
     * Update a template.
     */
    public function update(int $id, UpdateTemplateRequest $request)
    {
        $template = $this->templateService->update($id, $request->validated());

        return redirect()
            ->route('e-sign.jenis-surat.edit', $template->letter_type_id)
            ->with('success', 'Template berhasil diperbarui!');
    }

    /**
     * Delete a template.
     */
    public function destroy(int $id)
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
    public function setActive(int $id)
    {
        $template = $this->templateService->setActive($id);

        return redirect()
            ->route('e-sign.jenis-surat.edit', $template->letter_type_id)
            ->with('success', 'Template aktif berhasil diperbarui.');
    }

    /**
     * Preview template content.
     */
    public function preview(int $id)
    {
        $rendered = $this->templateService->previewContent($id);
        $template = $this->templateService->find($id);
        $placeholders = config('esign.placeholders', []);

        // Return JSON jika request dari AJAX (parameter ?ajax=1) atau wants JSON header
        if (request()->wantsJson() || request()->has('ajax')) {
            return response()->json([
                'rendered' => $rendered,
                'title' => $template->title,
                'version' => $template->version,
            ]);
        }

        return view('pages.e-sign.templates.preview', compact('template', 'rendered', 'placeholders'));
    }
}
