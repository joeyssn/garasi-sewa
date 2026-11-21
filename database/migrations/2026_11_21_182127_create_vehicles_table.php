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
    Schema::create('vehicles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('type', 20);                                // bicycle | motorcycle | car | electric_car
        $table->string('plate_number', 15)->nullable();            // empty for bicycles
        $table->string('brand_model', 100);
        $table->decimal('battery_capacity_kwh', 8, 2)->nullable(); // electric cars only
        $table->timestamps();
    });
}
};
