<?php

namespace App\Http\Controllers;

use App\Models\WarrantyClaim;
use App\Models\WarrantyPolicy;
use App\Http\Requests\StoreWarrantyClaimRequest;
use App\Http\Requests\UpdateWarrantyClaimRequest;
use App\Services\WarrantyClaimService;

class WarrantyClaimController extends Controller
{
    public function __construct(
        private WarrantyClaimService $service
    ) {}

    public function index()
    {
        $claims = WarrantyClaim::with([
            'warrantyPolicy.appliance.customer',
            'payout',
        ])->latest('claim_id')->paginate(10);

        return response()->json($claims);
    }

    public function create()
    {
        return response()->json([
            'warranty_policies' => WarrantyPolicy::with(
                'appliance.customer'
            )->get(),
            'required_fields' => [
                'warranty_policy_id',
                'claim_date',
                'defect_description',
            ],
        ]);
    }

    public function store(StoreWarrantyClaimRequest $request)
    {
        $claim = $this->service->storeClaim(
            $request->validated()
        );

        return response()->json([
            'message' => $claim->claim_status === 'rejected'
                ? 'Claim registered and automatically rejected.'
                : 'Warranty claim submitted successfully.',
            'data' => $claim,
        ], 201);
    }

    public function update(
        UpdateWarrantyClaimRequest $request,
        $id
    ) {
        $claim = $this->service->updateClaim(
            (int) $id,
            $request->validated()
        );

        return response()->json([
            'message' => 'Warranty claim updated.',
            'data' => $claim,
        ]);
    }
}