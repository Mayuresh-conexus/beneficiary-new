<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $roles = "'" . implode("','", \App\Models\User::getRoles()) . "'";
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM($roles) NOT NULL DEFAULT 'volunteer'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'organization_admin', 'manager', 'volunteer', 'auditor') NOT NULL DEFAULT 'volunteer'");
    }
};
