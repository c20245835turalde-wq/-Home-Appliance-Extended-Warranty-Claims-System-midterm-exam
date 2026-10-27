<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warranty_claims', function (Blueprint $table) {
    $table->id('claim_id');

    $table->foreignId('warranty_policy_id')
        ->constrained('warranty_policies', 'warranty_policy_id')
        ->onDelete('restrict');

    $table->date('claim_date');
    $table->text('defect_description');
    $table->string('claim_status', 30)->default('pending');
    $table->timestamps();

    $table->index(['claim_status', 'claim_date']);
});
    }

    public function down(): void
    {
        Schema::dropIfExists('warranty_claims');
    }
};
