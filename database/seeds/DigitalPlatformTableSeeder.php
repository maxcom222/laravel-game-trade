<?php

use Illuminate\Database\Seeder;

class DigitalPlatformTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('digital_platform')->delete();

        \DB::table('digital_platform')->insert(array (
            0 =>
            array (
                'platform_id' => 2,
                'digital_id' => 5,
            ),
            1 =>
            array (
                'platform_id' => 1,
                'digital_id' => 1,
            ),
            2 =>
            array (
                'platform_id' => 1,
                'digital_id' => 2,
            ),
            3 =>
            array (
                'platform_id' => 1,
                'digital_id' => 3,
            ),
            4 =>
            array (
                'platform_id' => 1,
                'digital_id' => 4,
            ),
            5 =>
            array (
                'platform_id' => 3,
                'digital_id' => 6,
            ),
            6 =>
            array (
                'platform_id' => 5,
                'digital_id' => 7,
            ),
            7 =>
            array (
                'platform_id' => 4,
                'digital_id' => 7,
            ),
            8 =>
            array (
                'platform_id' => 6,
                'digital_id' => 5,
            ),
            9 =>
            array (
                'platform_id' => 7,
                'digital_id' => 6,
            ),
            10 =>
            array (
                'platform_id' => 8,
                'digital_id' => 7,
            ),
            11 =>
            array (
                'platform_id' => 9,
                'digital_id' => 5,
            ),
        ));


    }
}
