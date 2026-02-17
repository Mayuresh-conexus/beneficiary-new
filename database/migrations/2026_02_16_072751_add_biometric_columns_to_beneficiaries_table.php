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
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->longText('biometric_template')->nullable()->after('status');
            $table->string('biometric_device')->nullable()->after('biometric_template');
            $table->timestamp('biometric_enrolled_at')->nullable()->after('biometric_device');
            $table->boolean('is_verified')->default(false)->after('biometric_enrolled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
        //
        });
    }
};
