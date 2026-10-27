<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarrantyPolicy extends Model
{
    protected $primaryKey = 'warranty_policy_id';

    protected $fillable = [
        'appliance_id',
        'purchase_date',
        'warranty_expiry',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
    ];

    public function appliance()
    {
        return $this->belongsTo(
            Appliance::class,
            'appliance_id',
            'appliance_id'
        );
    }

    public function claims()
    {
        return $this->hasMany(
            WarrantyClaim::class,
            'warranty_policy_id',
            'warranty_policy_id'
        );
    }
}