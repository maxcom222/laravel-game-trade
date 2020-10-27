<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('roles')->delete();

        \DB::table('roles')->insert(array (
            0 =>
            array (
                'id' => 1,
                'slug' => 'admin',
                'name' => 'Admin',
                'created_at' => '2016-11-30 18:32:22',
                'updated_at' => '2017-01-08 23:27:36',
            ),
            1 =>
            array (
                'id' => 2,
                'slug' => 'moderator',
                'name' => 'Moderator',
                'created_at' => '2017-01-13 20:08:37',
                'updated_at' => '2017-01-13 20:08:37',
            ),
        ));


    }
}
