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
            $table->string('contact_number_2')->nullable()->after('contact_number');
            $table->string('passport_number')->nullable()->after('government_id');
            $table->string('profile_image')->nullable()->after('last_name');
            $table->string('employment_status')->nullable();
            $table->string('occupation')->nullable();
            $table->string('beneficiary_type')->nullable(); // e.g. "Widow", "Orphan"...
            $table->integer('no_of_households')->default(1);

            $table->text('urgent_need')->nullable(); // Comma separated or JSON
            $table->text('detail_situation')->nullable();

            // Financials
            $table->decimal('monthly_income', 10, 2)->nullable();
            $table->decimal('monthly_expenditure', 10, 2)->nullable();
            $table->decimal('debts', 10, 2)->nullable();
            $table->decimal('asset_value', 10, 2)->nullable(); // was value_of_asset
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropColumn([
                'contact_number_2',
                'passport_number',
                'profile_image',
                'employment_status',
                'occupation',
                'beneficiary_type',
                'no_of_households',
                'urgent_need',
                'detail_situation',
                'monthly_income',
                'monthly_expenditure',
                'debts',
                'asset_value'
            ]);
        });
    }
};
