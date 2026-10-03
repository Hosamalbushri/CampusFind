<?php

namespace CampusFind\LostAndFound\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth('user')->user();

        return $user !== null && ($user->role?->permission_type === 'all' || $user->hasPermission('lost_found.settings.categories'));
    }

    public function rules(): array
    {
        return [
            'code'       => ['required', 'string', 'max:64', 'unique:lost_found_categories,code'],
            'is_active'  => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
