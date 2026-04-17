<?php

namespace App\Http\Requests;

use App\Rules\SafeText;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name'      => ['required', new SafeText, Rule::unique('categories')->where(fn (Builder $query) => $query->where('user_id', $userId))],
            'color'     => ['required', 'hex_color'],
            'parent_id' => ['nullable', Rule::exists('categories', 'id')->where(fn (Builder $query) => $query->where('user_id', $userId)->whereNull('parent_id'))],

        ];
    }
}
