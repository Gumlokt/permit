<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Company::create([
            'name' => 'ООО Рога и Копыта',
        ]);
    }
}

/**
 * php artisan db:seed --class= CompaniesTableSeeder
 */
