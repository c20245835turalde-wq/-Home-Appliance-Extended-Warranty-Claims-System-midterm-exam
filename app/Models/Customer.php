<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'customer_name',
        'customer_phone',
    ];

    public function appliances()
    {
        return $this->hasMany(Appliance::class, 'customer_id', 'customer_id');
    }
}