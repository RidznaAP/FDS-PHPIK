<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MetodeUji;

class MetodeUjiSeeder extends Seeder
{
    public function run(): void
    {
        $metodes = [
            'PCR', 'RT-PCR', 'Real-Time PCR (qPCR)', 'Sekuensing DNA', 
            'Isolasi Bakteri', 'Uji Biokimia', 'Uji Sensitivitas/Antibiogram', 
            'Natif/Scrapping', 'Sediaan Ulas (Smear)', 'Kultur Jamur', 
            'Pemeriksaan Mikroskopis Struktur Jamur', 'Pemeriksaan Jaringan (Slide)', 
            'Isolasi Virus', 'ELISA', 'IFAT'
        ];

        foreach ($metodes as $metode) {
            MetodeUji::updateOrCreate(['nama' => $metode], ['aktif' => true]);
        }
    }
}
