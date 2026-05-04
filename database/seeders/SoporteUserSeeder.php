<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SoporteUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'soporte'],
            [
                'id' => Str::uuid(),
                'name' => 'Usuario',
                'lastname' => 'Soporte',
                'email' => 'soporte@example.com',
                'phone' => '11111111111',
                'password' => bcrypt('soporte@123'),
                'rol' => User::ROLE_SUPPORT,
                'tenant_id' => null,
                'status' => User::ACTIVE,
            ]
        );
    }
}
