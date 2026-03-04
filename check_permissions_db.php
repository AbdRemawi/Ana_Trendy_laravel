<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Permissions in database:" . PHP_EOL;
$permissions = \Spatie\Permission\Models\Permission::all();
if ($permissions->isEmpty()) {
    echo "  No permissions found!" . PHP_EOL;
    echo PHP_EOL;
    echo "You need to seed the permissions first." . PHP_EOL;
} else {
    foreach ($permissions as $permission) {
        echo "  - {$permission->name}" . PHP_EOL;
    }
}

echo PHP_EOL;
echo "Roles in database:" . PHP_EOL;
$roles = \Spatie\Permission\Models\Role::all();
if ($roles->isEmpty()) {
    echo "  No roles found!" . PHP_EOL;
} else {
    foreach ($roles as $role) {
        echo "  - {$role->name}";
        if ($role->name === 'Admin') {
            echo " (has " . $role->permissions->count() . " permissions)";
        }
        echo PHP_EOL;
    }
}

echo PHP_EOL;
echo "Users in database:" . PHP_EOL;
$users = \App\Models\User::all();
foreach ($users as $user) {
    echo "  - ID: {$user->id}, Name: {$user->name}, Email: {$user->email}" . PHP_EOL;
    echo "    Direct permissions: " . $user->permissions->pluck('name')->implode(', ') ?: 'none' . PHP_EOL;
    echo "    Roles: " . $user->roles->pluck('name')->implode(', ') ?: 'none' . PHP_EOL;
    echo PHP_EOL;
}
