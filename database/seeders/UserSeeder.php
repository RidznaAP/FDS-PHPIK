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

        // Admin UPT (Lokal)
        User::create([
            'name' => 'Admin UPT Aceh',
            'email' => 'upt@fds.go.id',
            'password' => Hash::make('password123'),
            'role' => 'upt',
            'upt_asal' => 'Balai KHIT Aceh',
        ]);
    }
}
