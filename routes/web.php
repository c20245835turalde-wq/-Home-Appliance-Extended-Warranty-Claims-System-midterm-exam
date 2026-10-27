<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplianceController;
use App\Http\Controllers\WarrantyClaimController;

Route::resource('appliances', ApplianceController::class)
    ->only(['index', 'create', 'store', 'update']);

Route::resource('warranty-claims', WarrantyClaimController::class)
    ->only(['index', 'create', 'store', 'update']);