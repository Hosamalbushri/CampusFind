<?php

namespace CampusFind\LostAndFound\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFoundItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'integer', 'exists:lost_found_categories,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'found_at' => ['sometimes', 'date'],
            'found_location' => ['sometimes', 'string', 'max:255'],
            'distinguishing_marks' => ['nullable', 'string', 'max:2000'],
            'identifying_details' => ['nullable', 'string', 'max:2000'],
            'serial_fragment' => ['nullable', 'string', 'max:255'],
            'staff_notes' => ['nullable', 'string', 'max:2000'],
            'storage_location' => ['nullable', 'string', 'max:255'],
        ];
    }
}
