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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Core Relations
            $table->foreignId('beneficiary_id')->constrained('beneficiaries')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('set null');

            // Stakeholders
            $table->foreignId('financial_officer_id')->nullable()->constrained('users');
            $table->foreignId('delivered_by')->nullable()->constrained('users'); // Volunteer

            // Transaction Details
            $table->decimal('amount', 15, 2)->nullable(); // Can be null for dry rations
            $table->string('currency', 3)->default('USD');
            $table->string('status')->default('pending'); // pending, approved, delivered, rejected

            // Delivery Proof
            $table->timestamp('delivery_date')->nullable();
            $table->string('signature_path')->nullable();
            $table->string('document_path')->nullable(); // PDF or Form

            $table->text('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
