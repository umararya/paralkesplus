<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['username' => 'admin'], [
            'name' => 'Administrator', 'email' => 'admin@paralkes.id',
            'password' => Hash::make('admin123'), 'role' => 'admin', 'is_active' => true,
        ]);

        User::firstOrCreate(['username' => 'staff01'], [
            'name' => 'Staff Paralkes', 'email' => 'staff@paralkes.id',
            'password' => Hash::make('staff123'), 'role' => 'staff', 'is_active' => true,
        ]);

        User::firstOrCreate(['username' => 'manager01'], [
            'name' => 'Manager Paralkes', 'email' => 'manager@paralkes.id',
            'password' => Hash::make('manager123'), 'role' => 'manager', 'is_active' => true,
        ]);

        $this->command->info('✓ Users seeded: admin, staff01, manager01');
    }
}