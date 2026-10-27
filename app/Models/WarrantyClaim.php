<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarrantyClaim extends Model
{
    protected $primaryKey = 'claim_id';

    protected $fillable = [
        'warranty_policy_id',
        'claim_date',
        'defect_description',
        'claim_status',
    ];

    protected $casts = [
        'claim_date' => 'date',
    ];

    public function warrantyPolicy()
    {
        return $this->belongsTo(
            WarrantyPolicy::class,
            'warranty_policy_id',
            'warranty_policy_id'
        );
    }

    public function payout()
    {
        return $this->hasOne(
            ClaimPayout::class,
            'claim_id',
            'claim_id'
        );
    }
}