<?php

namespace App\Http\Requests\Prodi;

use App\Enums\JenjangProdi;
use App\Enums\StatusAkreditasiProdi;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProdiRequest extends FormRequest
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
        $prodiId = $this->route('id') ?? $this->route('prodi');
        if (is_object($prodiId)) {
            $prodiId = $prodiId->id;
        }

        return [
            'kode_prodi' => ['required', 'string', Rule::unique('prodi', 'kode_prodi')->ignore($prodiId)],
            'nama_prodi' => ['required', 'string'],
            'akreditasi' => ['nullable', 'string'],
            'jenjang' => ['required', Rule::enum(JenjangProdi::class)],
            'sk_pendirian' => ['nullable', 'string'],
            'tanggal_sk_pendirian' => ['nullable', 'date'],
            'status_akreditasi' => ['nullable', Rule::enum(StatusAkreditasiProdi::class)],
            'masa_berlaku_akreditasi' => ['nullable', 'date'],
            'id_upps' => ['nullable', 'exists:upps,id'],
        ];
    }

    /**
     * Custom message for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kode_prodi.unique' => 'Kode Prodi Sudah Ada',
        ];
    }
}
