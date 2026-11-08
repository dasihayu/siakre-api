<?php

namespace App\Http\Requests\EnrollmentMataKuliah;

use App\Enums\JenisDokumenBuktiPembelajaran;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEnrollmentMataKuliahRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare inputs for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('evidence') && is_array($this->evidence)) {
            $evidence = $this->evidence;

            // Jika format objek tunggal: { jenis_dokumen: ..., url: ... }
            if (isset($evidence['jenis_dokumen']) || isset($evidence['jenis']) || isset($evidence['url']) || isset($evidence['link_url'])) {
                $evidence = [$evidence];
            }

            // Jika format associative map: { "RPS": "https://...", "Kontrak Kuliah": "https://..." }
            $validTypes = array_map(fn ($case) => $case->value, JenisDokumenBuktiPembelajaran::cases());
            if (! array_is_list($evidence) && ! empty($evidence)) {
                $normalized = [];
                foreach ($evidence as $key => $val) {
                    if (in_array($key, $validTypes) && is_string($val)) {
                        $normalized[] = [
                            'jenis_dokumen' => $key,
                            'url' => $val,
                        ];
                    }
                }
                if (! empty($normalized)) {
                    $evidence = $normalized;
                }
            }

            // Normalisasi key di dalam list (jenis -> jenis_dokumen, link_url -> url)
            if (is_array($evidence) && array_is_list($evidence)) {
                $evidence = array_map(function ($item) {
                    if (! is_array($item)) {
                        return $item;
                    }

                    return [
                        'jenis_dokumen' => $item['jenis_dokumen'] ?? $item['jenis'] ?? null,
                        'url' => $item['url'] ?? $item['link_url'] ?? null,
                    ];
                }, $evidence);
            }

            $this->merge(['evidence' => $evidence]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tahun_akademik_id' => ['required', 'exists:tahun_akademik,id'],
            'mata_kuliah_id' => ['required', 'exists:mata_kuliah,id'],
            'dosen_id' => ['nullable', 'integer'],
            'evidence' => ['nullable', 'array'],
            'evidence.*.jenis_dokumen' => ['required', Rule::enum(JenisDokumenBuktiPembelajaran::class)],
            'evidence.*.url' => ['required', 'string', 'url'],
            'mata_kuliah_angkatan_kelas_id' => ['nullable', 'integer'],
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
            'tahun_akademik_id.required' => 'Tahun akademik wajib dipilih.',
            'tahun_akademik_id.exists' => 'Tahun akademik tidak ditemukan.',
            'mata_kuliah_id.required' => 'Mata kuliah wajib dipilih.',
            'mata_kuliah_id.exists' => 'Mata kuliah tidak ditemukan.',
            'evidence.array' => 'Data evidence harus berupa struktur data bukti pembelajaran.',
            'evidence.*.jenis_dokumen.required' => 'Jenis dokumen bukti pembelajaran wajib diisi.',
            'evidence.*.jenis_dokumen.Illuminate\Validation\Rules\Enum' => 'Jenis dokumen bukti pembelajaran harus salah satu dari: RPS, Kontrak Kuliah, Presensi, Soal Ujian.',
            'evidence.*.url.required' => 'Link URL bukti pembelajaran wajib diisi.',
            'evidence.*.url.url' => 'Format URL bukti pembelajaran tidak valid.',
        ];
    }
}

