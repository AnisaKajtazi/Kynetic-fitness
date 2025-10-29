<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    
    public function run(): void
    {
        
$this->call(RoleSeeder::class);

        User::factory()->create([
    'username' => 'testuser',
    'name' => 'Test User',
    'surname' => 'User',
    'email' => 'test@example.com',
    'RoleID' => 2, 
    'password' => bcrypt('password'), 
    'phone' => '1234567890',
    'address' => 'Test Address',
    'dob' => '2000-01-01',
    'gender' => 'other',
]);
    }
}
