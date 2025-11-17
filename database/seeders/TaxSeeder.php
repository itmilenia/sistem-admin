<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tr_tax')->insert(
            [
                [
                    'tax_name' => 'PPN',
                    'tax_rate' => 11.00,
                    'is_active' => true,
                    'created_by' => 1,
                    'updated_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]
        );
    }
}
