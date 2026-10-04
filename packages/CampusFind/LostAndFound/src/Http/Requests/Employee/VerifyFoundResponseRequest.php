<?php

namespace CampusFind\LostAndFound\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class VerifyFoundResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('user')->check();
    }

    public function rules(): array
    {
        return [
            'found_item_reference' => ['required', 'string', 'min:3', 'max:64', 'regex:/^[A-Za-z0-9\-_]+$/'],
            'verification_evidence' => ['required', 'string', 'max:2000'],
        ];
    }
}
