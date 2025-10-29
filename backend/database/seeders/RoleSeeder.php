<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['RoleID' => 1, 'name' => 'Admin', 'description' => 'Administrator with full access'],
            ['RoleID' => 2, 'name' => 'Client', 'description' => 'Regular client user'],
            ['RoleID' => 3, 'name' => 'Staff', 'description' => 'Staff member (Trainer, etc.)'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['RoleID' => $role['RoleID']], 
                [
                    'name' => $role['name'],
                    'description' => $role['description'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }
}
?>