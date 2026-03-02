<?php

namespace App\Imports;

use App\Models\Perencanaan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class PerencanaanImport implements ToModel, WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
        $user = Auth::user();
        
        // Skip validation if critical fields are empty
        if (!isset($row['provinsi']) || empty($row['provinsi'])) {
            return null;
        }

        // Determine who owns this data
        $ownerId = $user->id;

        // If PUSAT or BBKHIT, they might be importing for a specific BKHIT
        if (($user->isPusat() || $user->isBbkhit()) && isset($row['upt'])) {
            $upt = User::where('upt_asal', $row['upt'])->where('role', 'bkhit')->first();
            if ($upt) {
                // If BBKHIT, check if the UPT is under their coordination
                if ($user->isBbkhit() && $upt->parent_id !== $user->id) {
                    // Skip or handle error? For now skip
                    return null;
                }
                $ownerId = $upt->id;
            }
        }

        $tw1 = (int)($row['tw1'] ?? 0);
        $tw2 = (int)($row['tw2'] ?? 0);
        $tw3 = (int)($row['tw3'] ?? 0);
        $tw4 = (int)($row['tw4'] ?? 0);
        $total = $tw1 + $tw2 + $tw3 + $tw4;

        return new Perencanaan([
            'user_id'                 => $ownerId,
            'provinsi'                => $row['provinsi'],
            'kab_kota'                => $row['kab_kota'] ?? '-',
            'jenis_mp'                => $row['jenis_mp'] ?? '-',
            'jenis_hpik'              => $row['jenis_hpik'] ?? '-',
            'kemampuan_uji_upt'       => $row['kemampuan_uji_upt'] ?? '-',
            'metode_pengujian'        => $row['metode_pengujian'] ?? '-',
            'lab_uji'                 => $row['lab_uji'] ?? '-',
            'target_uji'              => (int)($row['target_uji'] ?? 0),
            'tw1'                     => $tw1,
            'tw2'                     => $tw2,
            'tw3'                     => $tw3,
            'tw4'                     => $tw4,
            'total_pengujian'         => $total,
            'rencana_lokasi'          => $row['lokasi_pengambilan_sampel'] ?? null,
            'rencana_jumlah_sampel'   => (int)($row['jumlah_sampel'] ?? 0),
            'rencana_metode_sampling' => $row['metode_pengambilan_sampel'] ?? null,
            'status'                  => 'draft',
        ]);
    }
}
