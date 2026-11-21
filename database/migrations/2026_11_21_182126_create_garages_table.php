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
    Schema::create('garages', function (Blueprint $table) {
        $table->id();
        $table->string('code', 4)->unique();                             // S001, C010, E005
        $table->string('type', 20)->index();                             // bicycle | car | electric
        $table->decimal('daily_price', 12, 2);
        $table->decimal('electricity_price_per_kwh', 12, 2)->nullable(); // electric garages only
        $table->string('status', 20)->default('available');              // available | used
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('garages');
}
};
