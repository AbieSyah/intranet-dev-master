<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::firstOrCreate([
            'name' => 'e-sign.menu',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'e-sign.profile',
            'guard_name' => 'web',
        ]);
    }
}
