<?php

namespace CampusFind\LostAndFound\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreFoundItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:lost_found_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'found_at' => ['required', 'date'],
            'found_location' => ['required', 'string', 'max:255'],
            'distinguishing_marks' => ['nullable', 'string', 'max:2000'],
            'identifying_details' => ['nullable', 'string', 'max:2000'],
            'serial_fragment' => ['nullable', 'string', 'max:255'],
            'staff_notes' => ['nullable', 'string', 'max:2000'],
            'reporter_student_id' => ['nullable', 'integer', 'exists:students,id'],
        ];
    }
}
