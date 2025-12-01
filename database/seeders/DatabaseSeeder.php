<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create SuperAdmin (not tied to any hospital)
        \App\Models\SuperAdmin::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@hospital.com',
            'password' => bcrypt('password'), // Change this in production!
            'phone' => '+254700000000',
        ]);

        // Create a demo hospital
        $hospital = \App\Models\Hospital::create([
            'name' => 'Demo Clinic',
            'email' => 'info@democlinic.com',
            'phone' => '+254711111111',
            'address' => '123 Medical Street, Nairobi, Kenya',
            'paybill_number' => '123456',
            'subscription_status' => 'active',
            'subscription_plan' => 'premium',
            'subscription_expires_at' => now()->addYear(),
            'settings' => [
                'working_hours' => '8:00 AM - 6:00 PM',
                'consultation_fee' => 1000,
            ],
        ]);

        // Create Admin for Demo Clinic
        \App\Models\User::create([
            'name' => 'Clinic Admin',
            'email' => 'admin@democlinic.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'phone' => '+254722222222',
            'is_active' => true,
            'hospital_id' => $hospital->id,
        ]);

        // Create Doctor for Demo Clinic
        \App\Models\User::create([
            'name' => 'Dr. John Doe',
            'email' => 'doctor@democlinic.com',
            'password' => bcrypt('password'),
            'role' => 'doctor',
            'phone' => '+254733333333',
            'is_active' => true,
            'hospital_id' => $hospital->id,
        ]);

        // Create Attendant for Demo Clinic
        \App\Models\User::create([
            'name' => 'Jane Smith',
            'email' => 'attendant@democlinic.com',
            'password' => bcrypt('password'),
            'role' => 'attendant',
            'phone' => '+254744444444',
            'is_active' => true,
            'hospital_id' => $hospital->id,
        ]);

        echo "✅ Database seeded successfully!\n";
        echo "📧 SuperAdmin: superadmin@hospital.com | Password: password\n";
        echo "📧 Admin: admin@democlinic.com | Password: password\n";
        echo "📧 Doctor: doctor@democlinic.com | Password: password\n";
        echo "📧 Attendant: attendant@democlinic.com | Password: password\n";
    }
}
