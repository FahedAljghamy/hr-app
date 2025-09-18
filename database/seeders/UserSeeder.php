<?php

/**
 * Author: Eng.Fahed
 * Professional User Seeder for HR System Testing
 */

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@hrsystem.com',
            'password' => Hash::make('password123'),
            'tenant_id' => null,
            'user_type' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ Created Super Admin user');

        // Get all tenants and create admin users for each
        $tenants = Tenant::all();
        
        foreach ($tenants as $tenant) {
            // Create Tenant Admin
            User::create([
                'name' => $tenant->company_name . ' Admin',
                'email' => $tenant->contact_email,
                'password' => Hash::make('admin123'),
                'tenant_id' => $tenant->id,
                'user_type' => 'tenant_admin',
                'email_verified_at' => now(),
            ]);

            // Create sample employees for each tenant
            $employeeCount = rand(5, min(20, $tenant->max_employees));
            
            for ($i = 1; $i <= $employeeCount; $i++) {
                $firstName = $this->getRandomFirstName();
                $lastName = $this->getRandomLastName();
                $email = strtolower($firstName . '.' . $lastName . '@' . $tenant->domain);

                User::create([
                    'name' => $firstName . ' ' . $lastName,
                    'email' => $email,
                    'password' => Hash::make('employee123'),
                    'tenant_id' => $tenant->id,
                    'user_type' => 'employee',
                    'email_verified_at' => now(),
                ]);
            }

            $this->command->info("✅ Created admin + {$employeeCount} employees for {$tenant->company_name}");
        }

        $this->command->info('✅ User seeding completed successfully!');
    }

    private function getRandomFirstName()
    {
        $firstNames = [
            'Ahmed', 'Mohammed', 'Ali', 'Omar', 'Hassan', 'Khalid', 'Saeed', 'Abdullah',
            'Sarah', 'Fatima', 'Aisha', 'Mariam', 'Nour', 'Layla', 'Zainab', 'Hala',
            'John', 'Michael', 'David', 'James', 'Robert', 'William', 'Richard', 'Charles',
            'Emily', 'Jessica', 'Ashley', 'Sarah', 'Amanda', 'Jennifer', 'Lisa', 'Nancy'
        ];
        
        return $firstNames[array_rand($firstNames)];
    }

    private function getRandomLastName()
    {
        $lastNames = [
            'Al-Sweida', 'Al-Hassan', 'Al-Karam', 'Al-Mahmoud', 'Al-Rashid', 'Al-Zahra',
            'Al-Mansour', 'Al-Najjar', 'Al-Baghdadi', 'Al-Damascus', 'Al-Homsi', 'Al-Aleppo',
            'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis',
            'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas'
        ];
        
        return $lastNames[array_rand($lastNames)];
    }
}
