<?php

namespace App\Http\Requests\Auth;

use App\Rules\PolinesEmailRule;
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
            'email' => ['required', 'string', 'email', new PolinesEmailRule],
            'name' => ['required', 'string'],
            'avatar' => ['nullable', 'string'],
        ];
    }
}
