<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define Permissions
        $permissions = [
            // User Management
            ['name' => 'view_users', 'display_name' => 'View Users', 'group' => 'User Management'],
            ['name' => 'create_users', 'display_name' => 'Create Users', 'group' => 'User Management'],
            ['name' => 'edit_users', 'display_name' => 'Edit Users', 'group' => 'User Management'],
            ['name' => 'delete_users', 'display_name' => 'Delete Users', 'group' => 'User Management'],

            // Role Management
            ['name' => 'view_roles', 'display_name' => 'View Roles', 'group' => 'Role Management'],
            ['name' => 'manage_roles', 'display_name' => 'Manage Roles', 'group' => 'Role Management'],

            // Project Management
            ['name' => 'view_projects', 'display_name' => 'View Projects', 'group' => 'Project Management'],
            ['name' => 'create_projects', 'display_name' => 'Create Projects', 'group' => 'Project Management'],
            ['name' => 'edit_projects', 'display_name' => 'Edit Projects', 'group' => 'Project Management'],
            ['name' => 'delete_projects', 'display_name' => 'Delete Projects', 'group' => 'Project Management'],

            // Beneficiary Management
            ['name' => 'view_beneficiaries', 'display_name' => 'View Beneficiaries', 'group' => 'Beneficiary Management'],
            ['name' => 'create_beneficiaries', 'display_name' => 'Create Beneficiaries', 'group' => 'Beneficiary Management'],
            ['name' => 'review_beneficiaries', 'display_name' => 'Review Beneficiaries', 'group' => 'Beneficiary Management'],

            // Organization Management
            ['name' => 'view_organizations', 'display_name' => 'View Organizations', 'group' => 'Organization Management'],
            ['name' => 'manage_organizations', 'display_name' => 'Manage Organizations', 'group' => 'Organization Management'],
        ];

        foreach ($permissions as $permission) {
            \App\Models\Permission::updateOrCreate(['name' => $permission['name']], $permission);
        }

        // Define Roles
        $superAdmin = \App\Models\Role::updateOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Admin']);
        $orgAdmin = \App\Models\Role::updateOrCreate(['name' => 'organization_admin'], ['display_name' => 'Organization Admin']);
        $manager = \App\Models\Role::updateOrCreate(['name' => 'manager'], ['display_name' => 'Manager']);
        $volunteer = \App\Models\Role::updateOrCreate(['name' => 'volunteer'], ['display_name' => 'Volunteer']);

        // Assign all permissions to Super Admin
        $superAdmin->permissions()->sync(\App\Models\Permission::all());

        // Assign some permissions to Org Admin
        $orgAdmin->permissions()->sync(\App\Models\Permission::whereIn('group', ['User Management', 'Project Management', 'Beneficiary Management'])->get());
    }
}
