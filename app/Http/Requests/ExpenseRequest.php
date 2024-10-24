<?php

namespace App\Http\Requests;

use App\Enums\Currency;
use App\Enums\PaymentMethod;
use App\Rules\AlphaSpace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payee'               => ['required', new AlphaSpace],
            'amount'              => 'required|decimal:0,2',
            'is_business_expense' => 'required|boolean',
            'currency'            => ['required', Rule::in(Currency::names())],
            'payment_method'      => Rule::in(PaymentMethod::values()),
            'transaction_date'    => 'required|date_format:Y-m-d',
            'effective_date'      => 'required|date_format:Y-m-d',
            'category_id'         => ['required', 'numeric', Rule::in(Auth::user()->categoryIds)],
            'notes'               => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.in' => 'Invalid category selection.',
        ];
    }
}
