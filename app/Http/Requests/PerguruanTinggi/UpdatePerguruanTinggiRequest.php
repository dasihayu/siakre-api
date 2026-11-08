<?php

namespace App\Http\Requests\PerguruanTinggi;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePerguruanTinggiRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kode_pt' => ['required', 'string', 'max:50'],
            'nama_pt' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'pimpinan' => ['nullable', 'string', 'max:255'],
            'visi' => ['nullable', 'string'],
            'misi' => ['nullable', 'string'],
            'sk_pendirian' => ['nullable', 'string', 'max:100'],
            'tanggal_sk_pendirian' => ['nullable', 'date'],
        ];
    }
}
