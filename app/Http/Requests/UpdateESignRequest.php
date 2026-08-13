<?php

namespace App\Http\Requests;

use App\Models\ESign;
use Illuminate\Foundation\Http\FormRequest;

class UpdateESignRequest extends FormRequest
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
        return [
            'employee_id' => [
                'required',
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
            'tanggal_mulai' => [
                'required',
                'string',
            ],
            'tanggal_akhir' => [
                'nullable',
                'string',
            ],
            'title' => [
                'nullable',
                'string',
                'max:255',
            ],
            'content' => [
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
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'employee_id.required' => 'Pilih employee terlebih dahulu.',
            'tanggal_mulai.required' => 'Tanggal 1 wajib diisi.',
        ];
    }
}
