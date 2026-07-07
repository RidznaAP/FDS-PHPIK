<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Seed 40 admin accounts + 1 Admin Pusat.
     * Password seragam: hpik2025
     */
    public function run(): void
    {
        $password = Hash::make('hpik2025');

        $accounts = [
            // ─── Admin Pusat (1 akun) ────────────────────────────────────
            [
                'name'     => 'Admin Pusat',
                'email'    => 'adminpusat@gmail.com',
                'role'     => 'pusat',
                'upt_asal' => null,
            ],

            // ─── BBKHIT-level (8 akun) ────────────────────────────────────
            [
                'name'     => 'BUSKHIT',
                'email'    => 'buskhit@gmail.com',
                'role'     => 'bbkhit',
                'upt_asal' => 'BUSKHIT',
            ],
            [
                'name'     => 'BUTTMKHIT',
                'email'    => 'buttmkhit@gmail.com',
                'role'     => 'bbkhit',
                'upt_asal' => 'BUTTMKHIT',
            ],
            [
                'name'     => 'BBKHIT Soekarno-Hatta',
                'email'    => 'bbkhitsoekarnohatta@gmail.com',
                'role'     => 'bbkhit',
                'upt_asal' => 'BBKHIT Soekarno-Hatta',
            ],
            [
                'name'     => 'BBKHIT Tanjung Priok',
                'email'    => 'bbkhittanjungpriok@gmail.com',
                'role'     => 'bbkhit',
                'upt_asal' => 'BBKHIT Tanjung Priok',
            ],
            [
                'name'     => 'BBKHIT Sumatera Utara',
                'email'    => 'bbkhitsumaterautara@gmail.com',
                'role'     => 'bbkhit',
                'upt_asal' => 'BBKHIT Sumatera Utara',
            ],
            [
                'name'     => 'BBKHIT Jawa Timur',
                'email'    => 'bbkhitjawatimur@gmail.com',
                'role'     => 'bbkhit',
                'upt_asal' => 'BBKHIT Jawa Timur',
            ],
            [
                'name'     => 'BBKHIT Sulawesi Selatan',
                'email'    => 'bbkhitsulawesiselatan@gmail.com',
                'role'     => 'bbkhit',
                'upt_asal' => 'BBKHIT Sulawesi Selatan',
            ],
            [
                'name'     => 'BBKHIT Papua',
                'email'    => 'bbkhitpapua@gmail.com',
                'role'     => 'bbkhit',
                'upt_asal' => 'BBKHIT Papua',
            ],

            // ─── BKHIT (32 akun) ─────────────────────────────────────────
            [
                'name'     => 'BKHIT Aceh',
                'email'    => 'bkhitaceh@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Aceh',
            ],
            [
                'name'     => 'BKHIT Riau',
                'email'    => 'bkhitriau@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Riau',
            ],
            [
                'name'     => 'BKHIT Kepulauan Riau',
                'email'    => 'bkhitkepulauanriau@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Kepulauan Riau',
            ],
            [
                'name'     => 'BKHIT Jambi',
                'email'    => 'bkhitjambi@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Jambi',
            ],
            [
                'name'     => 'BKHIT Sumatera Barat',
                'email'    => 'bkhitsumaterabarat@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Sumatera Barat',
            ],
            [
                'name'     => 'BKHIT Sumatera Selatan',
                'email'    => 'bkhitsumateraselatan@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Sumatera Selatan',
            ],
            [
                'name'     => 'BKHIT Kepulauan Bangka Belitung',
                'email'    => 'bkhitkepulauanbangkabelitung@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Kepulauan Bangka Belitung',
            ],
            [
                'name'     => 'BKHIT Bengkulu',
                'email'    => 'bkhitbengkulu@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Bengkulu',
            ],
            [
                'name'     => 'BKHIT Lampung',
                'email'    => 'bkhitlampung@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Lampung',
            ],
            [
                'name'     => 'BKHIT Banten',
                'email'    => 'bkhitbanten@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Banten',
            ],
            [
                'name'     => 'BKHIT Jawa Barat',
                'email'    => 'bkhitjawabarat@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Jawa Barat',
            ],
            [
                'name'     => 'BKHIT Jawa Tengah',
                'email'    => 'bkhitjawatengah@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Jawa Tengah',
            ],
            [
                'name'     => 'BKHIT DI Yogyakarta',
                'email'    => 'bkhitdiyogyakarta@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT DI Yogyakarta',
            ],
            [
                'name'     => 'BKHIT Bali',
                'email'    => 'bkhitbali@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Bali',
            ],
            [
                'name'     => 'BKHIT Nusa Tenggara Barat',
                'email'    => 'bkhitnusatenggarabarat@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Nusa Tenggara Barat',
            ],
            [
                'name'     => 'BKHIT Nusa Tenggara Timur',
                'email'    => 'bkhitnusatenggaratimur@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Nusa Tenggara Timur',
            ],
            [
                'name'     => 'BKHIT Kalimantan Barat',
                'email'    => 'bkhitkalimantanbarat@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Kalimantan Barat',
            ],
            [
                'name'     => 'BKHIT Kalimantan Tengah',
                'email'    => 'bkhitkalimantantengah@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Kalimantan Tengah',
            ],
            [
                'name'     => 'BKHIT Kalimantan Selatan',
                'email'    => 'bkhitkalimantanselatan@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Kalimantan Selatan',
            ],
            [
                'name'     => 'BKHIT Kalimantan Timur',
                'email'    => 'bkhitkalimantantimur@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Kalimantan Timur',
            ],
            [
                'name'     => 'BKHIT Kalimantan Utara',
                'email'    => 'bkhitkalimantanutara@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Kalimantan Utara',
            ],
            [
                'name'     => 'BKHIT Sulawesi Utara',
                'email'    => 'bkhitsulawesiutara@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Sulawesi Utara',
            ],
            [
                'name'     => 'BKHIT Gorontalo',
                'email'    => 'bkhitgorontalo@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Gorontalo',
            ],
            [
                'name'     => 'BKHIT Sulawesi Tengah',
                'email'    => 'bkhitsulawesitengah@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Sulawesi Tengah',
            ],
            [
                'name'     => 'BKHIT Sulawesi Barat',
                'email'    => 'bkhitsulawesibarat@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Sulawesi Barat',
            ],
            [
                'name'     => 'BKHIT Sulawesi Tenggara',
                'email'    => 'bkhitsulawesitenggara@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Sulawesi Tenggara',
            ],
            [
                'name'     => 'BKHIT Maluku',
                'email'    => 'bkhitmaluku@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Maluku',
            ],
            [
                'name'     => 'BKHIT Maluku Utara',
                'email'    => 'bkhitmalukuutara@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Maluku Utara',
            ],
            [
                'name'     => 'BKHIT Papua Barat',
                'email'    => 'bkhitpapuabarat@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Papua Barat',
            ],
            [
                'name'     => 'BKHIT Papua Barat Daya',
                'email'    => 'bkhitpapuabaratdaya@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Papua Barat Daya',
            ],
            [
                'name'     => 'BKHIT Papua Tengah',
                'email'    => 'bkhitpapuatengah@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Papua Tengah',
            ],
            [
                'name'     => 'BKHIT Papua Selatan',
                'email'    => 'bkhitpapuaselatan@gmail.com',
                'role'     => 'bkhit',
                'upt_asal' => 'BKHIT Papua Selatan',
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($accounts as $data) {
            $existing = User::where('email', $data['email'])->first();

            if ($existing) {
                $existing->update([
                    'name'     => $data['name'],
                    'role'     => $data['role'],
                    'upt_asal' => $data['upt_asal'],
                    'password' => $password,
                ]);
                $updated++;
            } else {
                User::create(array_merge($data, ['password' => $password]));
                $created++;
            }
        }

        $this->command->info("Selesai! Dibuat: {$created} akun, Diperbarui: {$updated} akun.");
        $this->command->info("Total akun sesuai daftar: " . count($accounts));
    }
}
