<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
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
            'to_user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $toUserId = $this->input('to_user_id');

            if ($toUserId == auth()->id()) {
                $validator->errors()->add('to_user_id', 'You cannot pay yourself. (-_-)');
                return;
            }

            if ($toUserId) {
                $colocation = $this->route('colocation');
                $recipientMembership = $colocation->memberships()
                    ->where('user_id', $toUserId)
                    ->whereNull('left_at')
                    ->first();

                if (!$recipientMembership) {
                    $validator->errors()->add('to_user_id', 'The selected recipient is not an active member of this colocation. (-_-)');
                }
            }
        });
    }
}
