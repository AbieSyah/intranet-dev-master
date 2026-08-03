<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
}
