<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== BRAND PERMISSIONS ===\n";
$brandPermissions = \App\Models\Permission::where('name', 'like', '%brand%')->get(['name', 'id']);
if ($brandPermissions->isEmpty()) {
    echo "❌ No brand permissions found!\n";
} else {
    foreach ($brandPermissions as $p) {
        echo "✓ {$p->name} (ID: {$p->id})\n";
    }
}

echo "\n=== SUPER_ADMIN ROLE ===\n";
$superAdmin = \Spatie\Permission\Models\Role::where('name', 'super_admin')->first();
if (!$superAdmin) {
    echo "❌ super_admin role not found!\n";
} else {
    echo "✓ super_admin role exists (ID: {$superAdmin->id})\n";
    echo "\n--- Brand Permissions for super_admin ---\n";
    $superAdminBrandPerms = $superAdmin->permissions()->where('name', 'like', '%brand%')->get(['name']);
    if ($superAdminBrandPerms->isEmpty()) {
        echo "❌ super_admin does NOT have brand permissions!\n";
    } else {
        foreach ($superAdminBrandPerms as $p) {
            echo "✓ {$p->name}\n";
        }
    }
}

echo "\n=== USER CHECK ===\n";
$user = \App\Models\User::find(1);
if (!$user) {
    echo "❌ User ID 1 not found!\n";
} else {
    echo "✓ User found: {$user->name} (ID: {$user->id})\n";
    echo "Roles: " . $user->roles->pluck('name')->implode(', ') . "\n";
    echo "Is Super Admin: " . ($user->isSuperAdmin() ? 'Yes' : 'No') . "\n";

    echo "\n--- User's Brand Permissions ---\n";
    $userBrandPerms = $user->permissions()->where('name', 'like', '%brand%')->get(['name']);
    if ($userBrandPerms->isEmpty()) {
        $viaRole = $user->getPermissionsViaRoles()->where('name', 'like', '%brand%');
        if ($viaRole->isEmpty()) {
            echo "❌ User does NOT have brand permissions!\n";
        } else {
            echo "Via roles:\n";
            foreach ($viaRole as $p) {
                echo "✓ {$p->name}\n";
            }
        }
    } else {
        foreach ($userBrandPerms as $p) {
            echo "✓ {$p->name}\n";
        }
    }

    echo "\n--- Permission Check Test ---\n";
    echo "can('view brands'): " . ($user->can('view brands') ? '✓ YES' : '❌ NO') . "\n";
}
