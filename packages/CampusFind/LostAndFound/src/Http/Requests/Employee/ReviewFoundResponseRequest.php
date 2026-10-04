<?php

namespace CampusFind\LostAndFound\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class ReviewFoundResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('user')->check();
    }

    public function rules(): array
    {
        return ['notes' => ['nullable', 'string', 'max:2000']];
    }
}
