<?php

namespace Database\Seeders;

use App\Models\Person;
use Illuminate\Database\Seeder;

class PersonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Person::create([
            'surname' => 'Иванов',
            'forename' => 'Иван',
            'patronymic' => 'Иванович',
            'position' => 'Главный архитектор',
        ]);
    }
}

/**
 * php artisan db:seed --class= PersonsTableSeeder
 */
