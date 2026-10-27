<?php

namespace App\Http\Controllers;

use App\Models\Appliance;
use App\Models\Customer;
use App\Http\Requests\StoreApplianceRequest;
use App\Http\Requests\UpdateApplianceRequest;
use App\Services\WarrantyClaimService;
use Illuminate\Http\Request;

class ApplianceController extends Controller
{
    public function __construct(
        private WarrantyClaimService $service
    ) {}

    public function index()
    {
        $appliances = Appliance::with([
            'customer',
            'warrantyPolicies',
        ])->latest('appliance_id')->paginate(10);

        return response()->json($appliances);
    }

    public function create()
    {
        return response()->json([
            'customers' => Customer::select(
                'customer_id',
                'customer_name'
            )->get(),
            'required_fields' => [
                'customer_id',
                'appliance_type',
                'model_number',
                'serial_number',
                'purchase_date',
                'warranty_expiry',
            ],
        ]);
    }

    public function store(StoreApplianceRequest $request)
    {
        $appliance = $this->service->storeAppliance(
            $request->validated()
        );

        return response()->json([
            'message' => 'Appliance registered successfully.',
            'data' => $appliance,
        ], 201);
    }

    public function update(
        UpdateApplianceRequest $request,
        $id
    ) {
        $appliance = $this->service->updateAppliance(
            (int) $id,
            $request->validated()
        );

        return response()->json([
            'message' => 'Appliance updated successfully.',
            'data' => $appliance,
        ]);
    }
}