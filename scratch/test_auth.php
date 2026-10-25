<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;

echo "--- TEST CREDENTIALS ---" . PHP_EOL;

$res1 = Auth::attempt(['username' => 'admin', 'password' => 'Admin1234']);
echo "Login [admin / Admin1234]: " . ($res1 ? "BERHASIL" : "GAGAL") . PHP_EOL;

$res2 = Auth::attempt(['email' => 'admin@ekasrt.id', 'password' => 'Admin1234']);
echo "Login [admin@ekasrt.id / Admin1234]: " . ($res2 ? "BERHASIL" : "GAGAL") . PHP_EOL;

$res3 = Auth::attempt(['username' => 'budi_santoso', 'password' => 'Warga1234']);
echo "Login [budi_santoso / Warga1234]: " . ($res3 ? "BERHASIL" : "GAGAL") . PHP_EOL;

$user = Auth::user();
echo "Current Authenticated User Role: " . ($user ? $user->role : "none") . PHP_EOL;
echo "Is Admin? " . ($user && $user->isAdmin() ? "YA" : "TIDAK") . PHP_EOL;
