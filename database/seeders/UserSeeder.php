<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin Pusat (Nasional)
        User::create([
            'name' => 'Admin Pusat',
            'email' => 'pusat@fds.go.id',
            'password' => Hash::make('password123'),
            'role' => 'pusat',
            'upt_asal' => 'Direktorat Jenderal Perikanan Budidaya',
        ]);

        // Admin BBKHIT (Regional)
        User::create([
            'name' => 'Admin BBKHIT',
            'email' => 'bbkhit@fds.go.id',
            'password' => Hash::make('password123'),
            'role' => 'bbkhit',
            'upt_asal' => 'BBKHIT Jakarta',
        ]);

        // Admin BKHIT (Lokal)
        User::create([
            'name' => 'Admin BKHIT Aceh',
            'email' => 'bkhit@sip.go.id',
            'password' => Hash::make('password123'),
            'role' => 'bkhit',
            'upt_asal' => 'Balai KHIT Aceh',
        ]);
    }
}
