<?php

namespace App\Http\Requests;

use App\Models\LetterType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
}
