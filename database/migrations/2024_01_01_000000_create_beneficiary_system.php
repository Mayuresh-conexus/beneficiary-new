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
        // 1. Users Table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['super_admin', 'organization_admin', 'manager', 'volunteer', 'auditor'])->default('volunteer');
            $table->foreignId('organization_id')->nullable(); // Constraint added later to avoid improved circular dependency
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Organizations Table
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('contact_number')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // Add foreign key for users -> organization
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('organization_id')->references('id')->on('organizations')->nullOnDelete();
        });

        // 3. Programs Table
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Packages Table
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Monthly Ration Kit"
            $table->enum('type', ['financial', 'food', 'medical', 'education', 'other']);
            $table->decimal('value', 10, 2); // Monetary value
            $table->string('frequency'); // e.g., "Monthly", "One-time"
            $table->json('conditions')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Projects Table
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['planning', 'active', 'completed', 'cancelled'])->default('planning');
            $table->timestamps();
            $table->softDeletes();
        });

        // Pivot: Project Packages
        Schema::create('project_package', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Pivot: Project Users (Managers/Volunteers assigned to Project)
        Schema::create('project_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // 6. Beneficiaries Table
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('government_id')->unique(); // ID Proof Number
            $table->string('contact_number')->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            $table->foreignId('assigned_project_id')->nullable()->constrained('projects')->nullOnDelete();
            
            // Submission info
            $table->foreignId('submitted_by')->constrained('users'); // Volunteer
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'fraud'])->default('draft');
            
            $table->timestamps();
            $table->softDeletes();
        });

        // Pivot: Beneficiary Packages
        Schema::create('beneficiary_package', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // 7. Documents Table
        Schema::create('beneficiary_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained()->onDelete('cascade');
            $table->string('type'); // ID Proof, Income Proof, Photo
            $table->string('file_path');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });

        // 8. Reviews / Approval Logs
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained()->onDelete('cascade');
            $table->foreignId('manager_id')->constrained('users');
            $table->enum('action', ['approve', 'reject', 'fraud']);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // 9. Activity Logs (Custom implementation as requested)
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // 'created', 'updated', 'deleted', 'approved', 'rejected'
            $table->string('description');
            $table->string('subject_type')->nullable(); // Model class
            $table->unsignedBigInteger('subject_id')->nullable(); // Model ID
            $table->json('properties')->nullable(); // Changed attributes
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('beneficiary_documents');
        Schema::dropIfExists('beneficiary_package');
        Schema::dropIfExists('beneficiaries');
        Schema::dropIfExists('project_user');
        Schema::dropIfExists('project_package');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('programs');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
        });

        Schema::dropIfExists('organizations');
        Schema::dropIfExists('users');
    }
};
