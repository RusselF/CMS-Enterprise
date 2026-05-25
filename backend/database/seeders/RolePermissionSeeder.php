<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── Permissions ───────────────────────────────────

        $permissions = [
            // Content
            'content.view', 'content.create',
            'content.edit.own', 'content.edit.any',
            'content.delete.own', 'content.delete.any',
            'content.publish', 'content.schedule',

            // Media
            'media.view', 'media.upload',
            'media.edit', 'media.delete',

            // Comments
            'comment.view', 'comment.moderate', 'comment.delete',

            // Users
            'user.view', 'user.create',
            'user.edit', 'user.delete',
            'user.activate',

            // Roles
            'role.view', 'role.manage',

            // Analytics & Activity
            'analytics.view',
            'activity.view',

            // Settings
            'settings.view', 'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        // ─── Roles ─────────────────────────────────────────

        // Super Admin — all permissions, cannot be deleted
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'api']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin — all except super-admin management
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $admin->givePermissionTo(Permission::all());

        // Editor — manage all content + media + comments
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'api']);
        $editor->givePermissionTo([
            'content.view', 'content.create', 'content.edit.any', 'content.delete.any',
            'content.publish', 'content.schedule',
            'media.view', 'media.upload', 'media.edit', 'media.delete',
            'comment.view', 'comment.moderate', 'comment.delete',
        ]);

        // Author — own content + upload media
        $author = Role::firstOrCreate(['name' => 'author', 'guard_name' => 'api']);
        $author->givePermissionTo([
            'content.view', 'content.create', 'content.edit.own', 'content.delete.own',
            'media.view', 'media.upload',
            'comment.view',
        ]);

        // Viewer — read-only access
        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'api']);
        $viewer->givePermissionTo([
            'content.view',
            'media.view',
            'comment.view',
        ]);
    }
}
