<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('biometric_verified')->default(false)->after('signature_path');
            $table->timestamp('biometric_verified_at')->nullable()->after('biometric_verified');
            $table->string('biometric_device')->nullable()->after('biometric_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['biometric_verified', 'biometric_verified_at', 'biometric_device']);
        });
    }
};
