<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Employee;

echo "🔍 Checking user-employee relationships...\n\n";

// التحقق من المستخدمين الموظفين
$employeeUsers = User::where('user_type', 'employee')->with('employee')->get();

echo "📊 Employee Users Statistics:\n";
echo "Total employee users: " . $employeeUsers->count() . "\n";

$withEmployee = $employeeUsers->filter(function($user) {
    return $user->employee !== null;
});

$withoutEmployee = $employeeUsers->filter(function($user) {
    return $user->employee === null;
});

echo "Users with employee record: " . $withEmployee->count() . "\n";
echo "Users without employee record: " . $withoutEmployee->count() . "\n\n";

if ($withEmployee->count() > 0) {
    echo "✅ Sample users with employee records:\n";
    foreach ($withEmployee->take(5) as $user) {
        echo "- User: {$user->name} (ID: {$user->id}) -> Employee: {$user->employee->full_name} (ID: {$user->employee->id})\n";
    }
}

if ($withoutEmployee->count() > 0) {
    echo "\n⚠️ Users without employee records:\n";
    foreach ($withoutEmployee->take(5) as $user) {
        echo "- User: {$user->name} (ID: {$user->id}) - No employee record\n";
    }
}

echo "\n✅ Check completed!\n";
