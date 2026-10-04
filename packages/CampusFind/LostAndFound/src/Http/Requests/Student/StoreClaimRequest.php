<?php

namespace CampusFind\LostAndFound\Http\Requests\Student;

use CampusFind\LostAndFound\Enums\ItemStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('student')->check();
    }

    public function rules(): array
    {
        return [
            'found_item_id' => [
                'required',
                'integer',
                Rule::exists('lost_found_items', 'id')->whereIn('status', [
                    ItemStatus::REPORTED->value,
                    ItemStatus::IN_CUSTODY->value,
                ]),
            ],
            'statement' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function validatedData(): array
    {
        return $this->only([
            'found_item_id',
            'statement',
        ]);
    }
}
