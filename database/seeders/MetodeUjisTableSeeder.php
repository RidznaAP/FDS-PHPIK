<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MetodeUjisTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('metode_ujis')->delete();
        
        \DB::table('metode_ujis')->insert(array (
            0 => 
            array (
                'id' => 1,
                'nama' => 'PCR',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-04-29 07:15:18',
                'updated_at' => '2026-04-29 07:15:18',
            ),
            1 => 
            array (
                'id' => 2,
                'nama' => 'RT-PCR',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-04-29 07:15:18',
                'updated_at' => '2026-04-29 07:15:18',
            ),
            2 => 
            array (
                'id' => 3,
            'nama' => 'Real-Time PCR (qPCR)',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-04-29 07:15:18',
                'updated_at' => '2026-04-29 07:15:18',
            ),
            3 => 
            array (
                'id' => 4,
                'nama' => 'Sekuensing DNA',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-04-29 07:15:18',
                'updated_at' => '2026-04-29 07:15:18',
            ),
            4 => 
            array (
                'id' => 5,
                'nama' => 'Isolasi Bakteri',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-04-29 07:15:18',
                'updated_at' => '2026-04-29 07:15:18',
            ),
            5 => 
            array (
                'id' => 6,
                'nama' => 'Uji Biokimia',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-04-29 07:15:18',
                'updated_at' => '2026-04-29 07:15:18',
            ),
            6 => 
            array (
                'id' => 7,
                'nama' => 'Uji Sensitivitas/Antibiogram',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-04-29 07:15:18',
                'updated_at' => '2026-04-29 07:15:18',
            ),
            7 => 
            array (
                'id' => 8,
                'nama' => 'Natif/Scrapping',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-04-29 07:15:18',
                'updated_at' => '2026-04-29 07:15:18',
            ),
            8 => 
            array (
                'id' => 9,
            'nama' => 'Sediaan Ulas (Smear)',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-04-29 07:15:18',
                'updated_at' => '2026-04-29 07:15:18',
            ),
            9 => 
            array (
                'id' => 10,
                'nama' => 'Kultur Jamur',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-04-29 07:15:18',
                'updated_at' => '2026-04-29 07:15:18',
            ),
            10 => 
            array (
                'id' => 11,
                'nama' => 'Pemeriksaan Mikroskopis Struktur Jamur',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-04-29 07:15:18',
                'updated_at' => '2026-04-29 07:15:18',
            ),
            11 => 
            array (
                'id' => 12,
            'nama' => 'Pemeriksaan Jaringan (Slide)',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-04-29 07:15:18',
                'updated_at' => '2026-04-29 07:15:18',
            ),
            12 => 
            array (
                'id' => 13,
                'nama' => 'Isolasi Virus',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-04-29 07:15:18',
                'updated_at' => '2026-04-29 07:15:18',
            ),
            13 => 
            array (
                'id' => 14,
                'nama' => 'ELISA',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-04-29 07:15:18',
                'updated_at' => '2026-04-29 07:15:18',
            ),
            14 => 
            array (
                'id' => 15,
                'nama' => 'IFAT',
                'keterangan' => NULL,
                'aktif' => 1,
                'created_at' => '2026-04-29 07:15:18',
                'updated_at' => '2026-04-29 07:15:18',
            ),
        ));
        
        
    }
}