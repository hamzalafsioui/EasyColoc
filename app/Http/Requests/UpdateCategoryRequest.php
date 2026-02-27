<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $colocation = $this->route('colocation');
        $category = $this->route('category');

        if ($category->colocation_id !== $colocation->id) {
            return false;
        }

        $membership = $colocation->memberships()
            ->where('user_id', auth()->id())
            ->whereNull('left_at')
            ->first();

        return $membership && $membership->role === 'owner';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
}
