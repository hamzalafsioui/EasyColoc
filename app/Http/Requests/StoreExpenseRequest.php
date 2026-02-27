<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $colocation = $this->route('colocation');

        $membership = $colocation->memberships()
            ->where('user_id', auth()->id())
            ->whereNull('left_at')
            ->first();

        return $membership !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date|before_or_equal:today',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'paid_by' => 'nullable|exists:users,id',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $paidBy = $this->input('paid_by');

            if ($paidBy && $paidBy != auth()->id()) {
                $colocation = $this->route('colocation');
                $payerMembership = $colocation->memberships()
                    ->where('user_id', $paidBy)
                    ->whereNull('left_at')
                    ->first();

                if (!$payerMembership) {
                    $validator->errors()->add('paid_by', 'The selected payer is not an active member of this colocation. (-_-)');
                }
            }
        });
    }
}
