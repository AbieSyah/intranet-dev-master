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
        // Gabungkan slug hardcoded (legacy) dengan slug dinamis dari tabel letter_types,
        // agar jenis surat baru (mis. "pengumuman") juga lolos validasi.
        $jenisSuratSlugs = array_values(array_unique(array_merge(
            array_keys(ESign::getJenisSuratLabels()),
            \App\Models\LetterType::pluck('slug')->all()
        )));

        $multi = $this->input('multi_surat') === '1';

        $rules = [
            'multi_surat' => [
                'nullable',
                'in:0,1',
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
                'nullable',
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
            'send_now' => [
                'nullable',
                'in:0,1',
            ],
        ];

        if ($multi) {
            // Multi-surat: wajib ada daftar penerima
            $rules['recipients'] = ['required', 'array', 'min:1'];
            $rules['recipients.*.employee_id'] = ['required', 'integer', 'exists:employees,id'];
            $rules['recipients.*.content'] = ['nullable', 'string'];
        } else {
            // Surat tunggal: satu employee_id
            $rules['employee_id'] = ['required', 'integer', 'exists:employees,id'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'employee_id.required' => 'Pilih employee terlebih dahulu.',
            'employee_id.exists' => 'Employee yang dipilih tidak valid.',
            'recipients.required' => 'Pilih minimal 1 karyawan penerima untuk multi-surat.',
            'recipients.min' => 'Pilih minimal 1 karyawan penerima untuk multi-surat.',
            'recipients.*.employee_id.required' => 'Pilih karyawan penerima.',
            'recipients.*.employee_id.exists' => 'Karyawan penerima tidak valid.',
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
