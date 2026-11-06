<?php

namespace App\Http\Requests\Kurikulum;

use App\Enums\StatusKurikulum;
use App\Models\Kurikulum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreKurikulumRequest extends FormRequest
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
            'id_prodi' => ['required', 'exists:prodi,id'],
            'nama_kurikulum' => ['required', 'string'],
            'tahun_mulai' => ['nullable', 'integer'],
            'berlaku_sampai' => ['required', 'integer'],
            'status' => ['nullable', Rule::enum(StatusKurikulum::class)],
            'dokumen_url' => ['nullable', 'string', 'url'],
            'profil_lulusan' => ['nullable', 'string'],
            'sk_kurikulum' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id_prodi.required' => 'Program studi wajib dipilih.',
            'id_prodi.exists' => 'Program studi tidak ditemukan.',
            'nama_kurikulum.required' => 'Nama kurikulum wajib diisi.',
            'berlaku_sampai.required' => 'Tahun masa berakhir kurikulum wajib diisi.',
            'status.Illuminate\Validation\Rules\Enum' => 'Status kurikulum harus berupa Aktif atau Arsip.',
            'dokumen_url.url' => 'Format URL dokumen kurikulum tidak valid.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $idProdi = $this->input('id_prodi');
            $status = $this->input('status', StatusKurikulum::AKTIF->value);
            if ($status instanceof StatusKurikulum) {
                $status = $status->value;
            }

            if ($idProdi && $status === StatusKurikulum::AKTIF->value) {
                $activeExists = Kurikulum::where('id_prodi', $idProdi)
                    ->where('status', StatusKurikulum::AKTIF->value)
                    ->exists();

                if ($activeExists) {
                    $validator->errors()->add(
                        'status',
                        'Program studi sudah memiliki kurikulum Aktif. Hanya diperbolehkan 1 kurikulum Aktif per program studi.'
                    );
                }
            }
        });
    }
}

