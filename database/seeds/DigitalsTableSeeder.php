<?php

use Illuminate\Database\Seeder;

class DigitalsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('digitals')->delete();
        
        \DB::table('digitals')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Steam',
                'description' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2017-01-15 12:33:48',
                'updated_at' => '2017-01-15 12:33:48',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Origin',
                'description' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2017-01-15 12:33:55',
                'updated_at' => '2017-01-15 12:33:55',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Battle.net',
                'description' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2017-01-15 12:34:03',
                'updated_at' => '2017-01-15 12:34:03',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Uplay',
                'description' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2017-01-15 12:34:11',
                'updated_at' => '2017-01-15 12:34:11',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'PlayStation Network',
                'description' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2017-01-15 12:34:19',
                'updated_at' => '2017-01-15 12:34:19',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Xbox Live',
                'description' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2017-01-15 12:34:27',
                'updated_at' => '2017-01-15 12:34:27',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Nintendo eShop',
                'description' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2017-01-15 12:34:38',
                'updated_at' => '2017-01-15 12:34:38',
            ),
        ));
        
        
    }
}