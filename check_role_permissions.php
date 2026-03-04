<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Role Permissions Mapping:" . PHP_EOL;
echo "=========================" . PHP_EOL;
echo PHP_EOL;

$roles = \Spatie\Permission\Models\Role::with('permissions')->get();

foreach ($roles as $role) {
    echo "Role: {$role->name}" . PHP_EOL;
    echo "  Permissions ({$role->permissions->count()}):" . PHP_EOL;
    $hasManageBrands = false;
    foreach ($role->permissions as $permission) {
        $marker = $permission->name === 'manage brands' ? ' <-- THIS ONE!' : '';
        echo "    - {$permission->name}{$marker}" . PHP_EOL;
        if ($permission->name === 'manage brands') {
            $hasManageBrands = true;
        }
    }
    echo "  Has 'manage brands': " . ($hasManageBrands ? 'YES ✓' : 'NO ✗') . PHP_EOL;
    echo PHP_EOL;
}

echo PHP_EOL;
echo "Current user permissions check (assuming you're logged in as admin):" . PHP_EOL;
echo "=======================================================================" . PHP_EOL;
// Try to get user from session
$sessionId = request()->cookie('laravel_session');
if ($sessionId) {
    echo "Session ID found: " . substr($sessionId, 0, 20) . "..." . PHP_EOL;
} else {
    echo "No session found - need to test in browser" . PHP_EOL;
}
