<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => (string) Str::uuid(),
                'name' => 'Super',
                'lastname' => 'Admin',
                'email' => 'superadmin@druvle.com',
                'username' => 'superadmin',
                'password' => Hash::make('superadmin@123'),
                'phone' => "100-000-000",
                'rol' => 1, // 1 = SUPER ADMIN
                'status' => true,
                'tenant_id' => null,
                'creation_date' => now(),
                'update_date' => now(),
                'delete_date' => null,
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Admin',
                'lastname' => 'Druvle',
                'email' => 'admin@druvle.com',
                'username' => 'admin',
                'password' => Hash::make('admin@123'),
                'phone' => "200-000-000",
                'rol' => 2, // 2 = ADMIN
                'status' => true,
                'tenant_id' => null,
                'creation_date' => now(),
                'update_date' => now(),
                'delete_date' => null,
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Cajero',
                'lastname' => 'Druvle',
                'email' => 'caja@druvle.com',
                'username' => 'caja',
                'password' => Hash::make('caja@123'),
                'phone' => "300-000-000",
                'rol' => 3, // 3 = CAJERO
                'status' => true,
                'tenant_id' => null,
                'creation_date' => now(),
                'update_date' => now(),
                'delete_date' => null,
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Soporte',
                'lastname' => 'Druvle',
                'email' => 'soporte@druvle.com',
                'username' => 'soporte',
                'password' => Hash::make('soporte@123'),
                'phone' => "400-000-000",
                'rol' => 4, // 4 = SOPORTE
                'status' => true,
                'tenant_id' => null, // soporte no pertenece a ningún tenant
                'creation_date' => now(),
                'update_date' => now(),
                'delete_date' => null,
            ],
        ]);
    }
}
