<?php

use Illuminate\Database\Seeder;

class PlatformsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('platforms')->delete();

        \DB::table('platforms')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'PC',
                'description' => NULL,
                'color' => '#000000',
                'acronym' => 'pc',
                'cover_position' => 'left',
                'created_at' => NULL,
                'updated_at' => '2017-01-15 12:40:29',
            ),
            1 =>
            array (
                'id' => 2,
                'name' => 'PlayStation 4',
                'description' => 'Sony',
                'color' => '#003791',
                'acronym' => 'ps4',
                'cover_position' => 'left',
                'created_at' => '2016-11-30 20:58:21',
                'updated_at' => '2017-01-22 14:19:14',
            ),
            2 =>
            array (
                'id' => 3,
                'name' => 'Xbox One',
                'description' => 'Microsoft',
                'color' => '#107c10',
                'acronym' => 'xboxone',
                'cover_position' => 'center',
                'created_at' => '2016-11-30 20:58:52',
                'updated_at' => '2017-01-11 10:48:02',
            ),
            3 =>
            array (
                'id' => 4,
                'name' => 'Nintendo Switch',
                'description' => 'Nintendo',
                'color' => '#e60012',
                'acronym' => 'switch',
                'cover_position' => 'left',
                'created_at' => '2017-01-22 14:05:09',
                'updated_at' => '2017-01-22 14:05:09',
            ),
            4 =>
            array (
                'id' => 5,
                'name' => 'Wii U',
                'description' => 'Nintendo',
                'color' => '#009ac7',
                'acronym' => 'wii-u',
                'cover_position' => 'center',
                'created_at' => NULL,
                'updated_at' => '2017-01-11 10:47:57',
            ),
            5 =>
            array (
                'id' => 6,
                'name' => 'PlayStation 3',
                'description' => 'Sony',
                'color' => '#326db3',
                'acronym' => 'ps3',
                'cover_position' => 'left',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            6 =>
            array (
                'id' => 7,
                'name' => 'Xbox 360',
                'description' => 'Microsoft',
                'color' => '#a4c955',
                'acronym' => 'xbox360',
                'cover_position' => 'left',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            7 =>
            array (
                'id' => 8,
                'name' => 'Nintendo 3DS',
                'description' => 'Nintendo',
                'color' => '#c90f17',
                'acronym' => '3ds',
                'cover_position' => 'left',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            8 =>
            array (
                'id' => 9,
                'name' => 'PlayStation Vita',
                'description' => 'Sony',
                'color' => '#1654bd',
                'acronym' => 'vita',
                'cover_position' => 'left',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            9 =>
            array (
                'id' => 10,
                'name' => 'Nintendo DS',
                'description' => 'Nintendo',
                'color' => '#929497',
                'acronym' => 'ds',
                'cover_position' => 'left',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            10 =>
            array (
                'id' => 11,
                'name' => 'PlayStation 2',
                'description' => 'Sony',
                'color' => '#140c7a',
                'acronym' => 'ps2',
                'cover_position' => 'left',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            11 =>
            array (
                'id' => 12,
                'name' => 'Xbox',
                'description' => 'Microsoft',
                'color' => '#93c83e',
                'acronym' => 'xbox',
                'cover_position' => 'left',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            12 =>
            array (
                'id' => 13,
                'name' => 'PlayStation',
                'description' => 'Sony',
                'color' => '#4081bc',
                'acronym' => 'ps',
                'cover_position' => 'left',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            13 =>
            array (
                'id' => 14,
                'name' => 'Wii',
                'description' => 'Nintendo',
                'color' => '#32ccfe',
                'acronym' => 'wii',
                'cover_position' => 'right',
                'created_at' => NULL,
                'updated_at' => '2017-01-11 10:48:51',
            ),
            14 =>
            array (
                'id' => 15,
                'name' => 'Gamecube',
                'description' => 'Nintendo',
                'color' => '#663399',
                'acronym' => 'gamecube',
                'cover_position' => 'center',
                'created_at' => NULL,
                'updated_at' => '2017-01-11 10:49:10',
            ),
            15 =>
            array (
                'id' => 16,
                'name' => 'Nintendo 64',
                'description' => 'Nintendo',
                'color' => '#fdbf2d',
                'acronym' => 'n64',
                'cover_position' => 'left',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            16 =>
            array (
                'id' => 17,
                'name' => 'Game Boy Advance',
                'description' => 'Nintendo',
                'color' => '#1f00cc',
                'acronym' => 'gba',
                'cover_position' => 'left',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            17 =>
            array (
                'id' => 18,
                'name' => 'PlayStation Portable',
                'description' => 'Sony',
                'color' => '#8e92af',
                'acronym' => 'psp',
                'cover_position' => 'left',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            18 =>
            array (
                'id' => 19,
                'name' => 'Dreamcast',
                'description' => 'Sega',
                'color' => '#4365a2',
                'acronym' => 'dreamcast',
                'cover_position' => 'left',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));


    }
}
