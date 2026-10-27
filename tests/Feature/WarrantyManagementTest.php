<?php

namespace Tests\Feature;

use App\Models\Appliance;
use App\Models\Customer;
use App\Models\User;
use App\Models\WarrantyClaim;
use App\Models\WarrantyPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarrantyManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_appliance_registration_normalizes_customer_phone_and_identifiers(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('appliances.store'), [
            'customer_name' => '  Jane Smith  ',
            'customer_phone' => '+1 (555) 123-4567',
            'appliance_type' => ' Refrigerator ',
            'model_number' => ' ab-123 ',
            'serial_number' => ' sn-456 ',
            'purchase_date' => '2025-01-01',
            'warranty_expiry' => '2027-01-01',
        ])->assertRedirect(route('appliances.index'));

        $this->assertDatabaseHas('customers', [
            'name' => 'Jane Smith',
            'phone' => '+15551234567',
        ]);
        $this->assertDatabaseHas('appliances', [
            'model_number' => 'AB-123',
            'serial_number' => 'SN-456',
        ]);
        $policy = Appliance::query()->firstOrFail()->warrantyPolicies()->firstOrFail();
        $this->assertSame('2025-01-01', $policy->purchase_date->toDateString());
        $this->assertSame('2027-01-01', $policy->warranty_expiry->toDateString());
    }

    public function test_expired_claim_is_rejected_and_does_not_create_a_payout(): void
    {
        $this->actingAs(User::factory()->create());
        $policy = $this->createPolicy();

        $this->post(route('warranty-claims.store'), [
            'warranty_policy_id' => $policy->id,
            'claim_date' => '2026-01-02',
            'defect_description' => '  <b>Compressor failure</b>  ',
            'status' => 'approved',
            'approved_amount' => '125.50',
        ])->assertRedirect(route('warranty-claims.index'));

        $claim = WarrantyClaim::query()->firstOrFail();
        $this->assertSame('rejected', $claim->status);
        $this->assertSame('Compressor failure', $claim->defect_description);
        $this->assertDatabaseCount('claim_payouts', 0);
    }

    public function test_claim_before_purchase_date_is_rejected_by_validation(): void
    {
        $this->actingAs(User::factory()->create());
        $policy = $this->createPolicy();

        $this->from(route('warranty-claims.create'))
            ->post(route('warranty-claims.store'), [
                'warranty_policy_id' => $policy->id,
                'claim_date' => '2024-12-31',
                'defect_description' => 'Compressor failure',
                'status' => 'pending',
            ])
            ->assertSessionHasErrors('claim_date');

        $this->assertDatabaseCount('warranty_claims', 0);
    }

    public function test_in_warranty_claim_keeps_status_and_records_approved_amount(): void
    {
        $this->actingAs(User::factory()->create());
        $policy = $this->createPolicy();

        $this->post(route('warranty-claims.store'), [
            'warranty_policy_id' => $policy->id,
            'claim_date' => '2025-06-01',
            'defect_description' => 'Cooling failure',
            'status' => 'approved',
            'approved_amount' => '125.50',
        ])->assertRedirect(route('warranty-claims.index'));

        $this->assertDatabaseHas('warranty_claims', ['status' => 'approved']);
        $this->assertDatabaseHas('claim_payouts', ['amount' => 125.50]);
    }

    private function createPolicy(): WarrantyPolicy
    {
        $customer = Customer::query()->create(['name' => 'Jane Smith', 'phone' => '15551234567']);
        $appliance = Appliance::query()->create([
            'customer_id' => $customer->id,
            'appliance_type' => 'Refrigerator',
            'model_number' => 'AB-123',
            'serial_number' => 'SN-456',
        ]);

        return $appliance->warrantyPolicies()->create([
            'purchase_date' => '2025-01-01',
            'warranty_expiry' => '2025-12-31',
        ]);
    }
}
