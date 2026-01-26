<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('settings')->insert([
            [
                'id' => (string) Str::uuid(),
                'company_name' => 'Druvle S.A.S',
                'nit' => '123456789-0',
                'phone' => '+57 300 123 4567',
                'address' => 'Calle 123 #45-67, Valencia - España',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}