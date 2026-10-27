<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appliances', function (Blueprint $table) {
    $table->id('appliance_id');

    $table->foreignId('customer_id')
        ->constrained('customers', 'customer_id')
        ->onDelete('restrict');

    $table->string('appliance_type');
    $table->string('model_number', 100);
    $table->string('serial_number', 100)->unique();
    $table->timestamps();

    $table->index(['model_number', 'serial_number']);
});
    }
    public function down(): void
    {
        Schema::dropIfExists('appliances');
    }
};
