<?php

namespace App\Http\Requests;

use App\Models\LetterType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $letterTypeIds = LetterType::pluck('id')->toArray();

        return [
            'letter_type_id' => ['required', 'integer', Rule::in($letterTypeIds)],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'template_type' => ['required', 'string', Rule::in(['editor', 'docx', 'pdf', 'html'])],
            'file' => ['nullable', 'file', 'mimes:docx,pdf', 'max:10240'],
            'is_active' => ['boolean'],
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
            'letter_type_id.required' => 'Pilih jenis surat.',
            'letter_type_id.in' => 'Jenis surat tidak valid.',
            'title.required' => 'Judul template wajib diisi.',
            'template_type.required' => 'Pilih jenis template.',
            'template_type.in' => 'Jenis template tidak valid.',
            'file.mimes' => 'File harus bertipe DOCX atau PDF.',
            'file.max' => 'File maksimal 10MB.',
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
