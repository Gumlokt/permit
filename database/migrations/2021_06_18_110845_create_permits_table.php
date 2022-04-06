<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permits', function (Blueprint $table) {
            $table->id();

            $table->integer('people_id')->unsigned()->index();
            $table->integer('companies_id')->unsigned()->index();

            $table->integer('number')->index();

            $table->timestamp('start')->nullable();
            $table->timestamp('end')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permits');
    }
}

/** Database Seeding
 * php artisan make:seeder PermitsTableSeeder
 */
