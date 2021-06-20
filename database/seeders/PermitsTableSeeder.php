<?php

namespace Database\Seeders;

use App\Models\Permit;
use Illuminate\Database\Seeder;

class PermitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permit::create([
            'people_id' => '1',
            'companies_id' => '1',
            'number' => '1',
            'start' => '2021-07-01 00:00:01',
            'end' => '2021-12-31 23:59:59',
        ]);
    }
}

/**
 * php artisan db:seed --class= PermitsTableSeeder
 */
