<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$aceh = User::where('email', 'bkhitaceh@gmail.com')->first();
$jambi = User::where('email', 'bkhitjambi@gmail.com')->first();

echo "Aceh: " . ($aceh ? $aceh->id : 'Not Found') . "\n";
echo "Jambi: " . ($jambi ? $jambi->id : 'Not Found') . "\n";
