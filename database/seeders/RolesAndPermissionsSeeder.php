<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'department_view',
            'department_create',
            'department_edit',
            'department_delete',
            'location_view',
            'location_create',
            'location_edit',
            'location_delete',
            'role_view',
            'role_create',
            'role_edit',
            'role_delete',
            'permission_view',
            'permission_create',
            'permission_edit',
            'permission_delete',
            'settings_manage',
            'admin_access',
            // Document Management
            'document_upload',
            'document_view',
            'document_edit',
            'document_delete',
            'document_approve',
            'document_download',
            'document_restore',
            'document_view_deleted',

            // Compliance Reports
            'report_create',
            'report_view',
            'report_edit',
            'report_submit',
            'report_approve',

            'template_view',

            // User Management
            'user_create',
            'user_view',
            'user_edit',
            'user_delete',

            // System Configuration
            'template_manage',
            'workflow_configure',
            'system_settings',

            // Dashboard Access
            'view_admin_dashboard',
            'view_compliance_dashboard',
            'view_manager_dashboard',
            'view_user_dashboard',

            // Audit
            'audit_logs_view',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $manager = Role::create(['name' => 'manager']);
        $manager->givePermissionTo([
            'document_view',
            'document_approve',
            'report_view',
            'report_approve',
            'view_manager_dashboard',
            'audit_logs_view',
            'document_download',
            'document_view_deleted',
            'document_restore',

        ]);

        $compliance_officer = Role::create(['name' => 'compliance_officer']);
        $compliance_officer->givePermissionTo([
            'document_upload',
            'document_view',
            'document_download',
            'report_create',
            'report_view',
            'report_edit',
            'report_submit',
            'view_compliance_dashboard',
            'template_manage',
            'template_view',
        ]);

        $user = Role::create(['name' => 'user']);
        $user->givePermissionTo([
            'document_upload',
            'document_view',
            'document_edit',
            'document_delete',
            'document_download',
            'view_user_dashboard',
            'document_view_deleted',
            'report_create',
            'report_view',
        ]);

        $auditor = Role::create(['name' => 'auditor']);
        $auditor->givePermissionTo([
            'document_view',
            'report_view',
            'audit_logs_view',
        ]);
    }
}
