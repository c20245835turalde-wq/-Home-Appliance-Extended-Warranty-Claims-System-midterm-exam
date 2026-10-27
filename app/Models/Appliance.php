<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appliance extends Model
{
    protected $primaryKey = 'appliance_id';

    protected $fillable = [
        'customer_id',
        'appliance_type',
        'model_number',
        'serial_number',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function warrantyPolicies()
    {
        return $this->hasMany(
            WarrantyPolicy::class,
            'appliance_id',
            'appliance_id'
        );
    }
}