<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApplianceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Replace with your authorization rule.
    }

    public function rules(): array
    {
        $applianceId = $this->route('id')
            ?? $this->route('appliance');

        return [
            'customer_id' => 'sometimes|required|exists:customers,customer_id',
            'appliance_type' => 'sometimes|required|string|max:100',
            'model_number' => 'sometimes|required|string|max:100',
            'serial_number' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('appliances', 'serial_number')
                    ->ignore($applianceId, 'appliance_id'),
            ],
            'purchase_date' => 'sometimes|required|date',
            'warranty_expiry' => 'sometimes|required|date|after_or_equal:purchase_date',
        ];
    }

    public function messages(): array
    {
        return [
            'serial_number.unique' => 'This serial number is already registered.',
            'warranty_expiry.after_or_equal' =>
                'Warranty expiry must be on or after the purchase date.',
        ];
    }
}