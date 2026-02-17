<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // e.g., 'beneficiary_submitted', 'beneficiary_approved', 'beneficiary_rejected', 'fraud_flagged', 'project_assigned'
            $table->string('title');
            $table->text('message');
            $table->string('icon')->default('notifications'); // Material icon name
            $table->string('color')->default('primary'); // Color class: primary, emerald, amber, rose, sky
            $table->string('link')->nullable(); // URL to navigate to
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
