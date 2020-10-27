<?php

use Illuminate\Database\Seeder;

class LanguagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('languages')->delete();

        \DB::table('languages')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'English',
                'app_name' => 'english',
                'flag' => NULL,
                'abbr' => 'en',
                'script' => 'Latn',
                'native' => 'English',
                'active' => 1,
                'default' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            1 =>
            array (
                'id' => 2,
                'name' => 'German',
                'app_name' => 'german',
                'flag' => NULL,
                'abbr' => 'de',
                'script' => 'Latn',
                'native' => 'Deutsch',
                'active' => 1,
                'default' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));


    }
}
