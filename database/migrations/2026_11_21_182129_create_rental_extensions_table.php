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
    Schema::create('rental_extensions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('rental_id')->constrained()->cascadeOnDelete();
        $table->foreignId('admin_id')->constrained('users');
        $table->date('old_end_date');
        $table->date('new_end_date');
        $table->unsignedInteger('extra_days');
        $table->decimal('additional_cost', 14, 2);   // remaining amount due, always >= 0
        $table->string('payment_proof');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_extensions');
    }
};
