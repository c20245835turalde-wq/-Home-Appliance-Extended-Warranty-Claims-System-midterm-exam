<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClaimPayout extends Model
{
    protected $primaryKey = 'payout_id';

    protected $fillable = [
        'claim_id',
        'approved_amount',
    ];

    protected $casts = [
        'approved_amount' => 'decimal:2',
    ];

    public function claim()
    {
        return $this->belongsTo(
            WarrantyClaim::class,
            'claim_id',
            'claim_id'
        );
    }
}