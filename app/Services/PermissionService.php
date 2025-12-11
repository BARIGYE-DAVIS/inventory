<?php

namespace App\Services;

use App\Models\Permission;

class PermissionService
{
    /**
     * Get all permissions from config (grouped by category)
     */
    public static function getAllPermissions(): array
    {
        return config('permissions');
    }

    /**
     * Get all permissions as flat array
     */
    public static function getAllPermissionsFlat(): array
    {
        $flat = [];
        foreach (config('permissions') as $category => $permissions) {
            foreach ($permissions as $permission) {
                $permission['category'] = $category;
                $flat[] = $permission;
            }
        }
        return $flat;
    }

    /**
     * Ensure permission exists in database (create if not)
     */
    public static function ensurePermissionExists(string $permissionName): Permission
    {
        $permission = Permission::where('name', $permissionName)->first();

        if (!$permission) {
            // Find in config
            foreach (config('permissions') as $category => $permissions) {
                foreach ($permissions as $perm) {
                    if ($perm['name'] === $permissionName) {
                        $permission = Permission::create([
                            'name' => $perm['name'],
                            'display_name' => $perm['display_name'],
                            'category' => $category,
                            'description' => $perm['description'],
                        ]);
                        break 2;
                    }
                }
            }
        }

        return $permission;
    }

    /**
     * Sync all permissions from config to database
     */
    public static function syncPermissions(): int
    {
        $count = 0;
        
        foreach (config('permissions') as $category => $permissions) {
            foreach ($permissions as $perm) {
                Permission::updateOrCreate(
                    ['name' => $perm['name']],
                    [
                        'display_name' => $perm['display_name'],
                        'category' => $category,
                        'description' => $perm['description'],
                    ]
                );
                $count++;
            }
        }

        return $count;
    }

    /**
     * Get permission by name (from DB or create from config)
     */
    public static function getPermission(string $permissionName): ?Permission
    {
        $permission = Permission::where('name', $permissionName)->first();

        if (!$permission) {
            $permission = self::ensurePermissionExists($permissionName);
        }

        return $permission;
    }
}