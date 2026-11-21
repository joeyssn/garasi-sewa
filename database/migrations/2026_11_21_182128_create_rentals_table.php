<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('rentals', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
        $table->foreignId('garage_id')->constrained()->restrictOnDelete();
        $table->string('rental_type', 10);                 // daily | monthly
        $table->unsignedSmallInteger('days')->nullable();  // daily rentals only (1-30)
        $table->date('start_date');
        $table->date('end_date');                          // inclusive
        $table->decimal('base_cost', 14, 2);
        $table->decimal('charging_cost', 14, 2)->default(0);
        $table->decimal('total_cost', 14, 2);
        $table->string('payment_proof');                   // path of the uploaded image
        $table->timestamps();

        $table->index(['garage_id', 'start_date', 'end_date']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
