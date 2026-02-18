<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Organization;
use App\Models\Program;
use App\Models\Package;
use App\Models\Project;
use App\Models\Beneficiary;
use App\Models\ActivityLog;
use App\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Seed Roles & Permissions
        $this->call(RolesAndPermissionsSeeder::class);

        // Fetch Roles
        $roleSuperAdmin = Role::where('name', 'super_admin')->first();
        $roleOrgAdmin = Role::where('name', 'organization_admin')->first();
        $roleManager = Role::where('name', 'manager')->first();
        $roleVolunteer = Role::where('name', 'volunteer')->first();
        $roleAuditor = Role::where('name', 'auditor')->first();
        $roleFinancialOfficer = Role::where('name', 'financial_officer')->first();

        // 1. Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);
        $superAdmin->roles()->attach($roleSuperAdmin);

        // 2. Organization
        $org = Organization::create([
            'name' => 'Global Aid Foundation',
            'address' => '123 Aid Street, Nairobi, Kenya',
            'contact_number' => '+254 700 123 456',
            'created_by' => $superAdmin->id,
            'status' => 'active',
        ]);

        $org2 = Organization::create([
            'name' => 'HealthFirst International',
            'address' => '456 Medical Ave, Mumbai, India',
            'contact_number' => '+91 9000 123 456',
            'created_by' => $superAdmin->id,
            'status' => 'active',
        ]);

        // 3. Org Admins
        $orgAdmin = User::create([
            'name' => 'Org Admin',
            'email' => 'org@globalaid.org',
            'password' => Hash::make('password'),
            'role' => 'organization_admin',
            'organization_id' => $org->id,
            'is_active' => true,
        ]);
        $orgAdmin->roles()->attach($roleOrgAdmin);

        // 4. Programs
        $progFood = Program::create([
            'organization_id' => $org->id,
            'name' => 'Food Security Program',
            'description' => 'Providing monthly food rations to vulnerable families.',
            'is_active' => true,
        ]);

        $progHealth = Program::create([
            'organization_id' => $org->id,
            'name' => 'Rural Healthcare Access',
            'description' => 'Medical camps and insurance support for rural areas.',
            'is_active' => true,
        ]);

        $progEdu = Program::create([
            'organization_id' => $org2->id,
            'name' => 'Education For All',
            'description' => 'Scholarships and tech grants for underprivileged students.',
            'is_active' => true,
        ]);

        // 5. Packages
        $pkgRation = Package::create([
            'program_id' => $progFood->id,
            'name' => 'Standard Ration Kit',
            'type' => 'food',
            'value' => 50.00,
            'frequency' => 'Monthly',
        ]);

        $pkgCash = Package::create([
            'program_id' => $progFood->id,
            'name' => 'Emergency Cash Transfer',
            'type' => 'financial',
            'value' => 30.00,
            'frequency' => 'One-time',
        ]);

        $pkgMed = Package::create([
            'program_id' => $progHealth->id,
            'name' => 'Basic Medical Kit',
            'type' => 'medical',
            'value' => 75.00,
            'frequency' => 'Quarterly',
        ]);

        $pkgEdu = Package::create([
            'program_id' => $progEdu->id,
            'name' => 'Tech Education Grant',
            'type' => 'education',
            'value' => 200.00,
            'frequency' => 'One-time',
        ]);

        // 6. Projects
        $projNairobi = Project::create([
            'organization_id' => $org->id,
            'program_id' => $progFood->id,
            'name' => 'Nairobi East Distribution',
            'location' => 'Nairobi East, Kenya',
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'status' => 'active',
        ]);
        $projNairobi->packages()->attach([$pkgRation->id, $pkgCash->id]);

        $projRural = Project::create([
            'organization_id' => $org->id,
            'program_id' => $progHealth->id,
            'name' => 'Rural Health Camps 2024',
            'location' => 'Turkana County, Kenya',
            'start_date' => now(),
            'end_date' => now()->addMonths(12),
            'status' => 'active',
        ]);
        $projRural->packages()->attach([$pkgMed->id]);

        // 7. Manager & Volunteer
        $manager = User::create([
            'name' => 'Sarah Manager',
            'email' => 'sarah@globalaid.org',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'organization_id' => $org->id,
            'is_active' => true,
        ]);
        $manager->roles()->attach($roleManager);

        $volunteer = User::create([
            'name' => 'David Volunteer',
            'email' => 'david@globalaid.org',
            'password' => Hash::make('password'),
            'role' => 'volunteer',
            'organization_id' => $org->id,
            'is_active' => true,
        ]);
        $volunteer->roles()->attach($roleVolunteer);

        $volunteer2 = User::create([
            'name' => 'Amina Field Worker',
            'email' => 'amina@globalaid.org',
            'password' => Hash::make('password'),
            'role' => 'volunteer',
            'organization_id' => $org->id,
            'is_active' => true,
        ]);
        $volunteer2->roles()->attach($roleVolunteer);

        $auditor = User::create([
            'name' => 'Audit Officer',
            'email' => 'auditor@globalaid.org',
            'password' => Hash::make('password'),
            'role' => 'auditor',
            'organization_id' => $org->id,
            'is_active' => true,
        ]);
        $auditor->roles()->attach($roleAuditor);

        $fo = User::create([
            'name' => 'Financial Officer',
            'email' => 'fo@globalaid.org',
            'password' => Hash::make('password'),
            'role' => 'financial_officer',
            'organization_id' => $org->id,
            'is_active' => true,
        ]);
        $fo->roles()->attach($roleFinancialOfficer);

        // Assign users to projects
        $projNairobi->assignedUsers()->attach([$manager->id, $volunteer->id, $volunteer2->id]);
        $projRural->assignedUsers()->attach([$manager->id, $volunteer2->id]);

        // 8. Beneficiaries
        $ben1 = Beneficiary::create([
            'first_name' => 'Sarah', 'last_name' => 'Jenkins',
            'government_id' => 'ID12345678', 'gender' => 'female',
            'assigned_project_id' => $projNairobi->id,
            'submitted_by' => $volunteer->id,
            'status' => 'approved',
            'latitude' => -1.2921, 'longitude' => 36.8219,
            'address' => 'Eastleigh, Nairobi',
        ]);
        $ben1->packages()->attach([$pkgRation->id]);

        $ben2 = Beneficiary::create([
            'first_name' => 'Michael', 'last_name' => 'Chen',
            'government_id' => 'ID87654321', 'gender' => 'male',
            'assigned_project_id' => $projRural->id,
            'submitted_by' => $volunteer2->id,
            'status' => 'submitted',
            'latitude' => -1.2821, 'longitude' => 36.8119,
            'address' => 'Turkana South',
        ]);

        $ben3 = Beneficiary::create([
            'first_name' => 'Amara', 'last_name' => 'Diop',
            'government_id' => 'ID11223344', 'gender' => 'female',
            'assigned_project_id' => $projNairobi->id,
            'submitted_by' => $volunteer->id,
            'status' => 'fraud',
            'latitude' => -1.2721, 'longitude' => 36.8019,
            'address' => 'Kibera, Nairobi',
        ]);

        $ben4 = Beneficiary::create([
            'first_name' => 'Oscar', 'last_name' => 'Wright',
            'government_id' => 'ID55667788', 'gender' => 'male',
            'assigned_project_id' => $projNairobi->id,
            'submitted_by' => $volunteer->id,
            'status' => 'approved',
            'latitude' => -1.2621, 'longitude' => 36.7919,
            'address' => 'Mathare, Nairobi',
        ]);
        $ben4->packages()->attach([$pkgRation->id, $pkgCash->id]);

        Beneficiary::create([
            'first_name' => 'Fatima', 'last_name' => 'Hassan',
            'government_id' => 'ID99001122', 'gender' => 'female',
            'assigned_project_id' => $projNairobi->id,
            'submitted_by' => $volunteer2->id,
            'status' => 'under_review',
            'latitude' => -1.2521, 'longitude' => 36.7819,
            'address' => 'Dandora, Nairobi',
        ]);

        Beneficiary::create([
            'first_name' => 'James', 'last_name' => 'Ochieng',
            'government_id' => 'ID33445566', 'gender' => 'male',
            'assigned_project_id' => $projRural->id,
            'submitted_by' => $volunteer2->id,
            'status' => 'rejected',
            'latitude' => -1.2421, 'longitude' => 36.7719,
            'address' => 'Lodwar, Turkana',
        ]);

        // 9. Activity Logs
        ActivityLog::create(['user_id' => $superAdmin->id, 'action' => 'create', 'description' => 'Created Global Aid Foundation organization', 'created_at' => now()->subDays(5)]);
        ActivityLog::create(['user_id' => $orgAdmin->id, 'action' => 'create', 'description' => 'Created Food Security Program', 'created_at' => now()->subDays(4)]);
        ActivityLog::create(['user_id' => $manager->id, 'action' => 'create', 'description' => 'Started Nairobi East Distribution project', 'created_at' => now()->subDays(3)]);
        ActivityLog::create(['user_id' => $volunteer->id, 'action' => 'create', 'description' => 'Submitted Sarah Jenkins for Nairobi East project', 'created_at' => now()->subDays(2)]);
        ActivityLog::create(['user_id' => $manager->id, 'action' => 'approve', 'description' => 'Approved Sarah Jenkins submission', 'created_at' => now()->subDays(1)]);
        ActivityLog::create(['user_id' => $volunteer->id, 'action' => 'create', 'description' => 'Submitted Michael Chen for Rural Health camps', 'created_at' => now()->subHours(5)]);

        // 10. Sample Notifications
        // For Super Admin
        Notification::send($superAdmin->id, 'beneficiary_submitted', 'New Beneficiary Submitted', 'Sarah Jenkins has been submitted by David Volunteer for review.', [
            'link' => '/beneficiaries/' . $ben1->id,
            'created_at' => now()->subDays(2),
            'read_at' => now()->subDays(1),
        ]);
        Notification::send($superAdmin->id, 'beneficiary_approved', 'Beneficiary Approved', 'Sarah Jenkins has been approved by Sarah Manager.', [
            'link' => '/beneficiaries/' . $ben1->id,
            'created_at' => now()->subDays(1),
            'read_at' => now()->subHours(12),
        ]);
        Notification::send($superAdmin->id, 'fraud_flagged', 'Fraud Alert - Immediate Action Required', '⚠️ Amara Diop has been flagged as fraud by Sarah Manager. Immediate attention required.', [
            'link' => '/beneficiaries/' . $ben3->id,
            'created_at' => now()->subHours(8),
        ]);
        Notification::send($superAdmin->id, 'beneficiary_submitted', 'New Beneficiary Submitted', 'Michael Chen has been submitted by Amina Field Worker for review.', [
            'link' => '/beneficiaries/' . $ben2->id,
            'created_at' => now()->subHours(5),
        ]);
        Notification::send($superAdmin->id, 'review_needed', 'Review Pending', 'Fatima Hassan is pending review. Please take action.', [
            'link' => '/beneficiaries/5',
            'created_at' => now()->subHours(3),
        ]);
        Notification::send($superAdmin->id, 'beneficiary_submitted', 'New Beneficiary Submitted', 'Oscar Wright has been submitted by David Volunteer for Nairobi East Distribution.', [
            'link' => '/beneficiaries/' . $ben4->id,
            'created_at' => now()->subHours(1),
        ]);
        Notification::send($superAdmin->id, 'system', 'Welcome to ImpactNexus', 'Your admin dashboard is ready. Monitor beneficiary submissions, reviews, and program performance in real-time.', [
            'link' => '/dashboard',
            'created_at' => now()->subDays(5),
            'read_at' => now()->subDays(5),
        ]);

        // For Manager
        Notification::send($manager->id, 'beneficiary_submitted', 'New Beneficiary Submitted', 'Sarah Jenkins has been submitted by David Volunteer for review.', [
            'link' => '/beneficiaries/' . $ben1->id,
            'created_at' => now()->subDays(2),
            'read_at' => now()->subDays(1),
        ]);
        Notification::send($manager->id, 'beneficiary_submitted', 'New Beneficiary Submitted', 'Michael Chen has been submitted by Amina Field Worker for review.', [
            'link' => '/beneficiaries/' . $ben2->id,
            'created_at' => now()->subHours(5),
        ]);
        Notification::send($manager->id, 'review_needed', 'Review Pending', 'Fatima Hassan needs your review. Please take action.', [
            'link' => '/beneficiaries/5',
            'created_at' => now()->subHours(2),
        ]);

        // For Volunteer
        Notification::send($volunteer->id, 'beneficiary_approved', 'Beneficiary Approved', 'Sarah Jenkins has been approved by Sarah Manager.', [
            'link' => '/beneficiaries/' . $ben1->id,
            'created_at' => now()->subDays(1),
        ]);
        Notification::send($volunteer->id, 'fraud_flagged', 'Fraud Alert', '⚠️ Amara Diop has been flagged as fraud. Please review your submission.', [
            'link' => '/beneficiaries/' . $ben3->id,
            'created_at' => now()->subHours(8),
        ]);
        Notification::send($volunteer->id, 'beneficiary_approved', 'Beneficiary Approved', 'Oscar Wright has been approved by Sarah Manager.', [
            'link' => '/beneficiaries/' . $ben4->id,
            'created_at' => now()->subHours(4),
        ]);
    }
}
