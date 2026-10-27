<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
Schema::create('warranty_policies', function (Blueprint $table) {
    $table->id('warranty_policy_id');

    $table->foreignId('appliance_id')
        ->constrained('appliances', 'appliance_id')
        ->onDelete('restrict');

    $table->date('purchase_date');
    $table->date('warranty_expiry');
    $table->timestamps();

    $table->index(['purchase_date', 'warranty_expiry']);
});
    }

    public function down(): void
    {
        Schema::dropIfExists('warranty_policies');
    }
};
