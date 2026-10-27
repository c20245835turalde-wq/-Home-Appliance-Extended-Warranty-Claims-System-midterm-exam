<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWarrantyClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Replace with your authorization rule.
    }

    public function rules(): array
    {
        return [
            'warranty_policy_id' =>
                'sometimes|required|exists:warranty_policies,warranty_policy_id',
            'claim_date' => 'sometimes|required|date',
            'defect_description' => 'sometimes|required|string|max:5000',
            'approved_amount' => 'sometimes|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'warranty_policy_id.exists' => 'The selected warranty policy does not exist.',
            'approved_amount.min' => 'The approved amount cannot be negative.',
        ];
    }
}