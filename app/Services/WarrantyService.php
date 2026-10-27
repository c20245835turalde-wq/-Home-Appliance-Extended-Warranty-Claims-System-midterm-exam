<?php

namespace App\Services;

use App\Models\Appliance;
use App\Models\WarrantyClaim;
use App\Models\WarrantyPolicy;
use App\Models\ClaimPayout;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WarrantyClaimService
{
    private function cleanString(?string $value): ?string
    {
        return $value === null
            ? null
            : trim(strip_tags($value));
    }

    private function normalizeAppliance(array $data): array
    {
        foreach (['appliance_type', 'model_number', 'serial_number'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = $this->cleanString($data[$field]);
            }
        }

        if (isset($data['model_number'])) {
            $data['model_number'] = strtoupper($data['model_number']);
        }

        if (isset($data['serial_number'])) {
            $data['serial_number'] = strtoupper($data['serial_number']);
        }

        return $data;
    }

    public function storeAppliance(array $data): Appliance
    {
        return DB::transaction(function () use ($data) {
            $data = $this->normalizeAppliance($data);

            $appliance = Appliance::create([
                'customer_id' => $data['customer_id'],
                'appliance_type' => $data['appliance_type'],
                'model_number' => $data['model_number'],
                'serial_number' => $data['serial_number'],
            ]);

            WarrantyPolicy::create([
                'appliance_id' => $appliance->appliance_id,
                'purchase_date' => $data['purchase_date'],
                'warranty_expiry' => $data['warranty_expiry'],
            ]);

            return $appliance->load('customer', 'warrantyPolicies');
        });
    }

    public function updateAppliance(int $id, array $data): Appliance
    {
        return DB::transaction(function () use ($id, $data) {
            $appliance = Appliance::findOrFail($id);
            $data = $this->normalizeAppliance($data);

            $appliance->fill(array_intersect_key(
                $data,
                array_flip([
                    'customer_id',
                    'appliance_type',
                    'model_number',
                    'serial_number',
                ])
            ));

            $appliance->save();

            $policy = $appliance->warrantyPolicies()
                ->latest('warranty_policy_id')
                ->first();

            if (
                isset($data['purchase_date']) ||
                isset($data['warranty_expiry'])
            ) {
                if (!$policy) {
                    throw ValidationException::withMessages([
                        'warranty_policy_id' =>
                            'No warranty policy exists for this appliance.',
                    ]);
                }

                $purchaseDate = $data['purchase_date']
                    ?? $policy->purchase_date->toDateString();

                $expiryDate = $data['warranty_expiry']
                    ?? $policy->warranty_expiry->toDateString();

                if ($expiryDate < $purchaseDate) {
                    throw ValidationException::withMessages([
                        'warranty_expiry' =>
                            'Warranty expiry must be on or after purchase date.',
                    ]);
                }

                $policy->update([
                    'purchase_date' => $purchaseDate,
                    'warranty_expiry' => $expiryDate,
                ]);
            }

            return $appliance->load('customer', 'warrantyPolicies');
        });
    }

    private function determineClaimStatus(
        int $policyId,
        string $claimDate
    ): string {
        $policy = WarrantyPolicy::findOrFail($policyId);

        $date = date('Y-m-d', strtotime($claimDate));
        $purchase = $policy->purchase_date->toDateString();
        $expiry = $policy->warranty_expiry->toDateString();

        if ($date < $purchase || $date > $expiry) {
            return 'rejected';
        }

        return 'pending';
    }

    private function sanitizeClaim(array $data): array
    {
        if (isset($data['defect_description'])) {
            $data['defect_description'] =
                $this->cleanString($data['defect_description']);
        }

        return $data;
    }

    public function storeClaim(array $data): WarrantyClaim
    {
        return DB::transaction(function () use ($data) {
            $data = $this->sanitizeClaim($data);

            $status = $this->determineClaimStatus(
                (int) $data['warranty_policy_id'],
                $data['claim_date']
            );

            $claim = WarrantyClaim::create([
                'warranty_policy_id' => $data['warranty_policy_id'],
                'claim_date' => $data['claim_date'],
                'defect_description' => $data['defect_description'],
                'claim_status' => $status,
            ]);

            if (array_key_exists('approved_amount', $data)) {
                ClaimPayout::create([
                    'claim_id' => $claim->claim_id,
                    'approved_amount' =>
                        $status === 'rejected'
                            ? 0
                            : $data['approved_amount'],
                ]);
            }

            return $claim->load('warrantyPolicy.appliance', 'payout');
        });
    }

    public function updateClaim(int $id, array $data): WarrantyClaim
    {
        return DB::transaction(function () use ($id, $data) {
            $claim = WarrantyClaim::findOrFail($id);
            $data = $this->sanitizeClaim($data);

            $policyId = (int) (
                $data['warranty_policy_id']
                ?? $claim->warranty_policy_id
            );

            $claimDate = $data['claim_date']
                ?? $claim->claim_date->toDateString();

            $status = $this->determineClaimStatus(
                $policyId,
                $claimDate
            );

            $claim->update([
                'warranty_policy_id' => $policyId,
                'claim_date' => $claimDate,
                'defect_description' =>
                    $data['defect_description']
                    ?? $claim->defect_description,
                'claim_status' => $status,
            ]);

            if (array_key_exists('approved_amount', $data)) {
                $claim->payout()->updateOrCreate(
                    ['claim_id' => $claim->claim_id],
                    [
                        'approved_amount' =>
                            $status === 'rejected'
                                ? 0
                                : $data['approved_amount'],
                    ]
                );
            } elseif ($status === 'rejected' && $claim->payout) {
                $claim->payout->update(['approved_amount' => 0]);
            }

            return $claim->load('warrantyPolicy.appliance', 'payout');
        });
    }
}