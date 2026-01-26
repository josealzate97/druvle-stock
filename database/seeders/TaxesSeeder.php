<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TaxesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('taxes')->insert([
            ['id' => (string) Str::uuid(), 'name' => 'IVA General', 'rate' => 21.00, 'status' => true],
            ['id' => (string) Str::uuid(), 'name' => 'IVA Reducido', 'rate' => 10.00, 'status' => true],
            ['id' => (string) Str::uuid(), 'name' => 'IVA Superreducido', 'rate' => 4.00, 'status' => true],
        ]);
    }
}
