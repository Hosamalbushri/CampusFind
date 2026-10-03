<?php

namespace CampusFind\LostAndFound\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth('user')->user();

        return $user !== null && ($user->role?->permission_type === 'all' || $user->hasPermission('lost_found.settings.categories'));
    }

    public function rules(): array
    {
        return [
            'code'       => [
                'sometimes',
                'required',
                'string',
                'max:64',
                Rule::unique('lost_found_categories', 'code')->ignore($this->route('id')),
            ],
            'is_active'  => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
