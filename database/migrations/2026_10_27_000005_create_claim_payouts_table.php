<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_payouts', function (Blueprint $table) {
    $table->id('payout_id');

    $table->foreignId('claim_id')
        ->unique()
        ->constrained('warranty_claims', 'claim_id')
        ->onDelete('restrict');

    $table->decimal('approved_amount', 12, 2)->default(0);
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_payouts');
    }
};
