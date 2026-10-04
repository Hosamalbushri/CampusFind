<?php

namespace CampusFind\LostAndFound\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class CompleteHandoverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('verification_method') && $this->has('identity_verification_reference')) {
            $this->merge([
                'verification_method' => $this->input('identity_verification_reference'),
            ]);
        }

        if (! $this->has('verification_note') && $this->has('notes')) {
            $this->merge([
                'verification_note' => $this->input('notes'),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'verification_method' => ['required', 'string'],
            'verification_note' => ['nullable', 'string'],
        ];
    }
}
