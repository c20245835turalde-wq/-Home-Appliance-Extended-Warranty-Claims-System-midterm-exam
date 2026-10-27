<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplianceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Replace with your authorization rule.
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,customer_id',
            'appliance_type' => 'required|string|max:100',
            'model_number' => 'required|string|max:100',
            'serial_number' => 'required|string|max:100|unique:appliances,serial_number',
            'purchase_date' => 'required|date',
            'warranty_expiry' => 'required|date|after_or_equal:purchase_date',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.exists' => 'The selected customer does not exist.',
            'serial_number.unique' => 'This serial number is already registered.',
            'warranty_expiry.after_or_equal' =>
                'Warranty expiry must be on or after the purchase date.',
        ];
    }
}