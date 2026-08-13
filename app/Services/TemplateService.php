<?php

    namespace App\Services;

    use App\Models\ESign;
    use App\Models\ESignTemplate;
    use App\Models\LetterType;
    use Illuminate\Http\UploadedFile;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Storage;

    class TemplateService
    {

        /**
         * Get all templates, optionally filtered by letter_type_id and search keyword.
         */
        public function index($letterTypeId = null, $search = null)
        {
            $query = ESignTemplate::with('creator', 'updater', 'letterType');

            if ($letterTypeId) {
                $query->where('letter_type_id', $letterTypeId);
            }

            if ($search) {
                $query->where('title', 'LIKE', '%' . $search . '%');
            }

            return $query->orderBy('jenis_surat_slug')
                ->orderBy('version', 'desc')
                ->get();
        }

        /**
         * Get templates for a specific letter type.
         */
        public function getByLetterType(int $letterTypeId)
        {
            return ESignTemplate::with('creator', 'updater')
                ->where('letter_type_id', $letterTypeId)
                ->orderBy('version', 'desc')
                ->get();
        }

        /**
         * Find template by ID.
         */
        public function find(int $id): ESignTemplate
        {
            return ESignTemplate::with('creator', 'updater', 'letterType')->findOrFail($id);
        }

        /**
         * Create a new template with optional file upload.
         *
         * @param array $data Keys: letter_type_id, title, template_type, content, file, is_active
         */
        public function create(array $data): ESignTemplate
        {
            return DB::transaction(function () use ($data) {
                $letterType = LetterType::findOrFail($data['letter_type_id']);

                // Get next version number
                $lastVersion = ESignTemplate::where('letter_type_id', $data['letter_type_id'])
                    ->max('version') ?? 0;
                $nextVersion = $lastVersion + 1;

                // If setting as active, deactivate all others of this type
                if (!empty($data['is_active'])) {
                    ESignTemplate::where('letter_type_id', $data['letter_type_id'])
                        ->where('is_active', true)
                        ->update(['is_active' => false]);
                }

                $fileData = $this->handleFileUpload($data);

                $sign1 = $data['sign_1'] ?? true;
                $sign2 = $data['sign_2'] ?? false;
                $sign3 = $data['sign_3'] ?? false;
                $recipientFlags = $this->normalizeRecipientFlags($sign1, $sign2, $sign3, $data);

                $template = ESignTemplate::create([
                    'letter_type_id' => $data['letter_type_id'],
                    'jenis_surat_slug' => $letterType->slug,
                    'title' => $data['title'],
                    'content' => $fileData['content'],
                    'template_type' => $data['template_type'] ?? 'editor',
                    'file_path' => $fileData['file_path'],
                    'file_original_name' => $fileData['file_original_name'],
                    'version' => $nextVersion,
                    'is_active' => $data['is_active'] ?? false,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                    'sign_1' => $sign1,
                    'sign_2' => $sign2,
                    'sign_3' => $sign3,
                    'sign_1_is_recipient' => $recipientFlags[1],
                    'sign_2_is_recipient' => $recipientFlags[2],
                    'sign_3_is_recipient' => $recipientFlags[3],
                ]);

                return $template->fresh('creator', 'updater', 'letterType');
            });
        }

        /**
         * Update an existing template.
         */
        public function update(int $id, array $data): ESignTemplate
        {
            return DB::transaction(function () use ($id, $data) {
                $template = ESignTemplate::findOrFail($id);

                // If setting as active, deactivate all others of this type
                if (!empty($data['is_active'])) {
                    ESignTemplate::where('letter_type_id', $template->letter_type_id)
                        ->where('id', '!=', $id)
                        ->where('is_active', true)
                        ->update(['is_active' => false]);
                }

                // Layout settings — merge with defaults
                $layoutDefaults = [
                    'page_margin_top' => 25,
                    'page_margin_bottom' => 25,
                    'page_margin_left' => 25,
                    'page_margin_right' => 25,
                    'page_size' => 'A4',
                ];

                $updateData = [
                    'title' => $data['title'] ?? $template->title,
                    'template_type' => $data['template_type'] ?? $template->template_type,
                    'is_active' => $data['is_active'] ?? false,
                    'updated_by' => Auth::id(),
                    'page_margin_top' => $data['page_margin_top'] ?? $template->page_margin_top ?? $layoutDefaults['page_margin_top'],
                    'page_margin_bottom' => $data['page_margin_bottom'] ?? $template->page_margin_bottom ?? $layoutDefaults['page_margin_bottom'],
                    'page_margin_left' => $data['page_margin_left'] ?? $template->page_margin_left ?? $layoutDefaults['page_margin_left'],
                    'page_margin_right' => $data['page_margin_right'] ?? $template->page_margin_right ?? $layoutDefaults['page_margin_right'],
                    'page_size' => $data['page_size'] ?? $template->page_size ?? $layoutDefaults['page_size'],
                    'sign_1' => $data['sign_1'] ?? $template->sign_1 ?? true,
                    'sign_2' => $data['sign_2'] ?? $template->sign_2 ?? false,
                    'sign_3' => $data['sign_3'] ?? $template->sign_3 ?? false,
                ];

                $recipientFlags = $this->normalizeRecipientFlags(
                    $updateData['sign_1'],
                    $updateData['sign_2'],
                    $updateData['sign_3'],
                    $data
                );
                $updateData['sign_1_is_recipient'] = $recipientFlags[1];
                $updateData['sign_2_is_recipient'] = $recipientFlags[2];
                $updateData['sign_3_is_recipient'] = $recipientFlags[3];

                // Handle file upload
                $fileData = $this->handleFileUpload($data, $template);
                if ($fileData['content_changed']) {
                    $updateData['content'] = $fileData['content'];
                }
                if ($fileData['file_changed']) {
                    // Delete old file if exists
                    if ($template->file_path) {
                        Storage::disk('public')->delete($template->file_path);
                    }
                    $updateData['file_path'] = $fileData['file_path'];
                    $updateData['file_original_name'] = $fileData['file_original_name'];
                }

                $template->update($updateData);

                return $template->fresh('creator', 'updater', 'letterType');
            });
        }

        /**
         * Hitung flag slot penerima (sign_X_is_recipient) dari input form.
         *
         * Hanya slot yang aktif yang boleh ditandai sebagai penerima, dan maksimal
         * satu slot penerima per template. Bila lebih dari satu ditandai, slot
         * pertama (urut 1..3) yang dipertahankan.
         *
         * @return array{1: bool, 2: bool, 3: bool}
         */
        private function normalizeRecipientFlags(bool $sign1, bool $sign2, bool $sign3, array $data): array
        {
            $active = [1 => $sign1, 2 => $sign2, 3 => $sign3];
            $flags = [
                1 => (bool) ($data['sign_1_is_recipient'] ?? false),
                2 => (bool) ($data['sign_2_is_recipient'] ?? false),
                3 => (bool) ($data['sign_3_is_recipient'] ?? false),
            ];

            foreach ([1, 2, 3] as $slot) {
                if (!$active[$slot]) {
                    $flags[$slot] = false;
                }
            }

            $picked = false;
            foreach ([1, 2, 3] as $slot) {
                if ($flags[$slot] && !$picked) {
                    $picked = true;
                } elseif ($flags[$slot]) {
                    $flags[$slot] = false;
                }
            }

            return $flags;
        }

        /**
         * Set a template as the active version for its letter type.
         */
        public function setActive(int $id): ESignTemplate
        {
            return DB::transaction(function () use ($id) {
                $template = ESignTemplate::findOrFail($id);

                // Deactivate all others
                ESignTemplate::where('letter_type_id', $template->letter_type_id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);

                // Activate this one
                $template->update([
                    'is_active' => true,
                    'updated_by' => Auth::id(),
                ]);

                return $template->fresh('creator', 'updater', 'letterType');
            });
        }

        /**
         * Delete a template and its associated file.
         */
        public function delete(int $id): void
        {
            $template = ESignTemplate::findOrFail($id);

            // Check if any e_signs reference this jenis surat with active status
            $usedCount = ESign::where('jenis_surat_slug', $template->jenis_surat_slug)
                ->whereIn('status', ['draft', 'pending', 'waiting_employee'])
                ->count();

            if ($usedCount > 0) {
                throw new \RuntimeException(
                    "Template sedang digunakan oleh {$usedCount} surat aktif dan tidak dapat dihapus."
                );
            }

            // Delete associated file
            if ($template->file_path) {
                Storage::disk('public')->delete($template->file_path);
            }

            $template->delete();
        }

        /**
         * Preview HTML/Text template content with dummy data.
         */
        public function previewContent(int $id): string
        {
            $template = ESignTemplate::findOrFail($id);
            $content = $template->content;

            if (!$content) {
                return '<div class="text-muted text-center py-5">
                    <i class="ri-file-text-line fs-1"></i>
                    <p class="mt-2">Template ini menggunakan file (' . strtoupper($template->template_type) . ').</p>
                    <p>Tidak ada pratinjau teks yang tersedia.</p>
                </div>';
            }

            $dummyData = [
                '{{employee_name}}' => 'Abie Manager',
                '{{employee_nik}}' => 'ABI-001',
                '{{employee_position}}' => 'ASSISTANT MANAGER',
                '{{department}}' => 'HRD & GA',
                '{{nomor_surat}}' => 'PKWT/2026/001',
                '{{tanggal_mulai}}' => '01 Juli 2026',
                '{{tanggal_akhir}}' => '01 Juli 2028',
                '{{today}}' => '07 Juli 2026',
            ];

            return str_replace(array_keys($dummyData), array_values($dummyData), $content);
        }

        // ========== PRIVATE HELPERS ==========

        /**
         * Handle file upload for template.
         * For DOCX/PDF: store file, return path.
         * For HTML/Text: store content.
         */
        private function handleFileUpload(array $data, ?ESignTemplate $existing = null): array
        {
            $result = [
                'content' => null,
                'file_path' => null,
                'file_original_name' => null,
                'content_changed' => false,
                'file_changed' => false,
            ];

            $templateType = $data['template_type'] ?? ($existing->template_type ?? 'editor');

            if (in_array($templateType, ['docx', 'pdf'])) {
                // File-based template
                if (isset($data['file']) && $data['file'] instanceof UploadedFile) {
                    $file = $data['file'];
                    $path = $file->store('esign/templates', 'public');
                    $result['file_path'] = $path;
                    $result['file_original_name'] = $file->getClientOriginalName();
                    $result['file_changed'] = true;
                    $result['content'] = null;
                    $result['content_changed'] = true;
                } elseif ($existing && $existing->file_path) {
                    // Keep existing file
                    $result['file_path'] = $existing->file_path;
                    $result['file_original_name'] = $existing->file_original_name;
                    $result['content'] = null;
                }
            } else {
                // Text-based template (editor/html)
                $result['content'] = $data['content'] ?? ($existing->content ?? null);
                $result['content_changed'] = true;
                // Delete existing file if switching from file to text
                if ($existing && $existing->file_path) {
                    Storage::disk('public')->delete($existing->file_path);
                }
            }

            return $result;
        }
    }
