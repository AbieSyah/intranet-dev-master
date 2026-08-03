<?php

namespace App\Http\Requests;

use App\Models\ESign;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreESignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $jenisSuratSlugs = array_keys(ESign::getJenisSuratLabels());

        return [
            'employee_id' => [
                'required',
                'integer',
                'exists:employees,id',
            ],
            'employee1_signee_id' => [
                'nullable',
                'integer',
                'exists:employees,id',
            ],
            'employee2_signee_id' => [
                'nullable',
                'integer',
                'exists:employees,id',
            ],
            'employee3_signee_id' => [
                'nullable',
                'integer',
                'exists:employees,id',
            ],
            'letter_type_id' => [
                'required',
                'integer',
                'exists:letter_types,id',
            ],
            'template_id' => [
                'required',
                'integer',
                'exists:esign_templates,id',
            ],
            'jenis_surat_slug' => [
                'required',
                'string',
                Rule::in($jenisSuratSlugs),
            ],
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'content' => [
                'nullable',
                'string',
            ],
            'tanggal_mulai' => [
                'required',
                'string',
            ],
            'tanggal_akhir' => [
                'nullable',
                'string',
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'employee_id.required' => 'Pilih employee terlebih dahulu.',
            'employee_id.exists' => 'Employee yang dipilih tidak valid.',
            'letter_type_id.required' => 'Jenis surat tidak valid.',
            'letter_type_id.exists' => 'Jenis surat tidak ditemukan.',
            'template_id.required' => 'Template surat wajib dipilih.',
            'template_id.exists' => 'Template surat tidak ditemukan.',
            'jenis_surat_slug.required' => 'Jenis surat tidak valid.',
            'jenis_surat_slug.in' => 'Jenis surat tidak dikenal.',
            'title.required' => 'Judul surat wajib diisi.',
            'title.max' => 'Judul surat maksimal 255 karakter.',
            'tanggal_mulai.required' => 'Tanggal 1 wajib diisi.',
            'tanggal_akhir.date' => 'Format tanggal tidak valid.',
        ];
    }
}
