<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = auth()->user();

if ($user) {
    echo "Authenticated User ID: " . $user->id . PHP_EOL;
    echo "User Name: " . $user->name . PHP_EOL;
    echo "User Email: " . $user->email . PHP_EOL;
    echo PHP_EOL;
    echo "Permissions:" . PHP_EOL;
    $permissions = $user->getAllPermissions()->pluck('name')->toArray();
    if (empty($permissions)) {
        echo "  No permissions found!" . PHP_EOL;
    } else {
        foreach ($permissions as $permission) {
            echo "  - $permission" . PHP_EOL;
        }
    }
    echo PHP_EOL;
    echo "Has 'manage brands' permission: " . ($user->hasPermissionTo('manage brands') ? 'YES' : 'NO') . PHP_EOL;
} else {
    echo "No authenticated user found!" . PHP_EOL;
    echo "Please login first." . PHP_EOL;
}
