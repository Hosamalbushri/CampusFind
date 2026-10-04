<?php

namespace CampusFind\LostAndFound\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class RejectFoundResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('user')->check();
    }

    public function rules(): array
    {
        return ['notes' => ['required', 'string', 'max:2000']];
    }
}
