<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'template_type' => ['nullable', 'string', Rule::in(['editor', 'docx', 'pdf', 'html'])],
            'file' => ['nullable', 'file', 'mimes:docx,pdf', 'max:10240'],
            'is_active' => ['boolean'],
            // Layout settings
            'page_margin_top' => ['nullable', 'integer', 'min:5', 'max:100'],
            'page_margin_bottom' => ['nullable', 'integer', 'min:5', 'max:100'],
            'page_margin_left' => ['nullable', 'integer', 'min:5', 'max:100'],
            'page_margin_right' => ['nullable', 'integer', 'min:5', 'max:100'],
            'page_size' => ['nullable', 'string', Rule::in(['A4', 'Letter', 'Legal'])],
            'sign_1' => ['boolean'],
            'sign_2' => ['boolean'],
            'sign_3' => ['boolean'],
            'sign_1_is_recipient' => ['boolean'],
            'sign_2_is_recipient' => ['boolean'],
            'sign_3_is_recipient' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul template wajib diisi.',
            'title.max' => 'Judul template maksimal 255 karakter.',
            'file.mimes' => 'File harus bertipe DOCX atau PDF.',
            'file.max' => 'File maksimal 10MB.',
            'page_margin_top.min' => 'Margin minimal 5mm.',
            'page_margin_top.max' => 'Margin maksimal 100mm.',
            'page_margin_bottom.min' => 'Margin minimal 5mm.',
            'page_margin_bottom.max' => 'Margin maksimal 100mm.',
            'page_margin_left.min' => 'Margin minimal 5mm.',
            'page_margin_left.max' => 'Margin maksimal 100mm.',
            'page_margin_right.min' => 'Margin minimal 5mm.',
            'page_margin_right.max' => 'Margin maksimal 100mm.',
            'page_size.in' => 'Ukuran kertas harus A4, Letter, atau Legal.',
        ];
    }

    /**
     * Validasi tambahan: template editor wajib menyertakan placeholder {{nomor_surat}}.
     * Nomor surat harus ditempatkan oleh user di dalam template agar bisa digenerate.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $type = $this->input('template_type');
                $content = $this->input('content');

                // Hanya untuk template berbasis teks (editor/html).
                if (in_array($type, ['editor', 'html']) && is_string($content)) {
                    if (strpos($content, '{{nomor_surat}}') === false) {
                        $validator->errors()->add(
                            'content',
                            'Template wajib menyertakan placeholder {{nomor_surat}} pada isi surat.'
                        );
                    }
                }
            },
        ];
    }
}
