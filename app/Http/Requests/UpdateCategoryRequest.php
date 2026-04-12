<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Rules\SafeText;
use Illuminate\Container\Attributes\RouteParameter;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('category'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(#[RouteParameter('category')] Category $category): array
    {
        $userId = $this->user()->id;

        return [
            'name'      => ['required', new SafeText, Rule::unique('categories')->where(fn (Builder $query) => $query->where('user_id', $userId))->ignore($category->id)],
            'color'     => ['required', 'hex_color'],
            'parent_id' => ['nullable', Rule::exists('categories', 'id')->where(fn (Builder $query) => $query->where('user_id', $userId)->whereNull('parent_id'))],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        $category = $this->route('category');

        return [
            function (Validator $validator) use ($category) {
                if ($category->name === Category::DEFAULT_NAME && $this->validated('name') !== Category::DEFAULT_NAME) {
                    $validator->errors()->add('name', 'The default category cannot be renamed.');
                }

                $parentId = $this->validated('parent_id');

                if ($parentId && (int) $parentId === $category->id) {
                    $validator->errors()->add('parent_id', 'A category cannot be its own parent.');
                }

                if ($parentId && $category->children()->exists()) {
                    $validator->errors()->add('parent_id', 'A parent category with children cannot become a subcategory.');
                }
            },
        ];
    }
}
