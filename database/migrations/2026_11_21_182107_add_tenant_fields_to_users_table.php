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
    Schema::table('users', function (Blueprint $table) {
        $table->string('nik', 16)->nullable()->unique()->after('id'); // KTP number, empty for admin
        $table->string('phone', 20)->nullable()->after('name');
        $table->string('role', 20)->default('tenant')->after('password');   // admin | tenant
        $table->string('status', 20)->default('pending')->after('role');   // pending | approved | rejected
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropUnique(['nik']);
        $table->dropColumn(['nik', 'phone', 'role', 'status']);
    });
}
    
};
