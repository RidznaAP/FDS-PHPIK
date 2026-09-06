<?php

namespace App\Imports;

use App\Models\Perencanaan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class PerencanaanImport implements ToModel, WithHeadingRow, SkipsOnError, SkipsOnFailure, WithValidation
{
    use Importable;

    /** Kumpulkan baris yang gagal validasi (tidak freeze, hanya di-skip) */
    public array $failures = [];

    /** Kumpulkan baris yang error saat insert (tidak freeze) */
    public array $errors = [];

    /**
     * Aturan validasi per baris yang diimpor.
     * Semua kolom wajib diubah ke nullable agar tidak memblokir proses.
     * Validasi hard-crash digantikan oleh SkipsOnFailure.
     */
    public function rules(): array
    {
        return [
            'tahun'      => ['nullable', 'numeric'],
            'provinsi'   => ['nullable', 'string'],
            'kab_kota'   => ['nullable', 'string'],
            'jenis_mp'   => ['nullable', 'string'],
            'jenis_hpik' => ['nullable', 'string'],
            'lab_uji'    => ['nullable', 'string'],
            'tw1'        => ['nullable', 'numeric', 'min:0'],
            'tw2'        => ['nullable', 'numeric', 'min:0'],
            'tw3'        => ['nullable', 'numeric', 'min:0'],
            'tw4'        => ['nullable', 'numeric', 'min:0'],
            'target_uji' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Dipanggil saat validasi gagal pada suatu baris.
     * Baris di-skip, proses tetap berlanjut ke baris berikutnya (tidak freeze).
     */
    public function onFailure(Failure ...$failures): void
    {
        foreach ($failures as $failure) {
            $this->failures[] = $failure;
        }
    }

    /**
     * Dipanggil saat terjadi error PHP (exception) saat memproses baris.
     * Catat errornya, proses tetap berlanjut (tidak freeze).
     */
    public function onError(Throwable $e): void
    {
        $this->errors[] = $e->getMessage();
    }

    /**
     * Konversi baris Excel ke model Perencanaan.
     * Baris yang tidak memiliki kolom wajib (provinsi) akan di-skip.
     */
    public function model(array $row)
    {
        // Skip baris yang benar-benar kosong atau tidak punya kolom wajib
        $provinsi = trim($row['provinsi'] ?? '');
        if (empty($provinsi)) {
            return null;
        }

        $user    = Auth::user();
        $ownerId = $user->id;

        // Pusat/Developer/BBKHIT bisa import untuk UPT tertentu jika kolom 'upt' diisi
        if (($user->isPusat() || $user->isDeveloper() || $user->isBbkhit()) && !empty(trim($row['upt'] ?? ''))) {
            $upt = User::where('upt_asal', trim($row['upt']))->where('role', 'bkhit')->first();
            if ($upt) {
                // BBKHIT hanya untuk wilayahnya
                if ($user->isBbkhit() && $upt->parent_id !== $user->id) {
                    return null;
                }
                $ownerId = $upt->id;
            }
            // Jika UPT tidak ditemukan di DB tapi Pusat yang import,
            // tetap gunakan user_id Pusat agar data tidak hilang
        }

        $tw1   = (int) ($row['tw1'] ?? 0);
        $tw2   = (int) ($row['tw2'] ?? 0);
        $tw3   = (int) ($row['tw3'] ?? 0);
        $tw4   = (int) ($row['tw4'] ?? 0);
        $total = $tw1 + $tw2 + $tw3 + $tw4;

        $tahun = !empty($row['tahun']) ? (int) $row['tahun'] : (int) date('Y');

        return new Perencanaan([
            'user_id'                 => $ownerId,
            'tahun'                   => $tahun,
            'provinsi'                => $provinsi,
            'kab_kota'                => trim($row['kab_kota'] ?? '-') ?: '-',
            'jenis_mp'                => trim($row['jenis_mp'] ?? '-') ?: '-',
            'jenis_hpik'              => trim($row['jenis_hpik'] ?? '-') ?: '-',
            'kemampuan_uji_upt'       => trim($row['kemampuan_uji_upt'] ?? 'Tersedia') ?: 'Tersedia',
            'metode_pengujian'        => trim($row['metode_pengujian'] ?? '-') ?: '-',
            'lab_uji'                 => trim($row['lab_uji'] ?? '-') ?: '-',
            'target_uji'              => max(0, (int) ($row['target_uji'] ?? $total)),
            'tw1'                     => $tw1,
            'tw2'                     => $tw2,
            'tw3'                     => $tw3,
            'tw4'                     => $tw4,
            'total_pengujian'         => $total,
            'rencana_lokasi'          => trim($row['lokasi_pengambilan_sampel'] ?? '') ?: null,
            'rencana_jumlah_sampel'   => max(0, (int) ($row['jumlah_sampel'] ?? 0)),
            'rencana_metode_sampling' => trim($row['metode_pengambilan_sampel'] ?? '') ?: null,
            'status'                  => 'draft',
        ]);
    }

    /** Kembalikan ringkasan failures dengan baris & alasan */
    public function getFailureSummary(): string
    {
        $msgs = [];
        foreach ($this->failures as $f) {
            $msgs[] = "Baris {$f->row()}: " . implode(', ', $f->errors());
        }
        foreach ($this->errors as $e) {
            $msgs[] = "Error: {$e}";
        }
        return implode(' | ', array_slice($msgs, 0, 5)); // max 5 pesan
    }
}
