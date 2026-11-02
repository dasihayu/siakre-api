<?php

namespace App\Http\Requests\Cpl;

use App\Enums\KategoriCpl;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCplRequest extends FormRequest
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
        $idKurikulum = $this->input('id_kurikulum');

        return [
            'id_kurikulum' => ['required', 'exists:kurikulum,id'],
            'kode_cpl' => [
                'required',
                'string',
                Rule::unique('cpl', 'kode_cpl')->where(function ($query) use ($idKurikulum) {
                    return $query->where('id_kurikulum', $idKurikulum);
                }),
            ],
            'deskripsi_cpl' => ['required', 'string'],
            'kategori' => ['required', Rule::enum(KategoriCpl::class)],
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
            'kode_cpl.unique' => 'Kode CPL Sudah Ada',
        ];
    }
}
