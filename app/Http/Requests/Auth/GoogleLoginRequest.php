<?php

namespace App\Http\Requests\Auth;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GoogleLoginRequest extends FormRequest
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
            'google_id' => ['required', 'string'],
            'email' => [
                'required',
                'string',
                'email',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $domain = strtolower((string) substr(strrchr((string) $value, '@'), 1));
                    if ($domain !== 'polines.ac.id' && ! str_ends_with($domain, '.polines.ac.id')) {
                        $fail('Hanya email dengan domain *.polines.ac.id yang diizinkan.');
                    }
                },
            ],
            'name' => ['required', 'string'],
            'avatar' => ['nullable', 'string'],
        ];
    }
}
