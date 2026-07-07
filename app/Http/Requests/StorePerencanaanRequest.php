<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePerencanaanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Otorisasi pada Policy
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'provinsi'          => ['required', 'string', 'max:255'],
            'kab_kota'          => ['required', 'string', 'max:255'],
            'jenis_mp'          => ['required', 'string', 'max:255'],
            'jenis_hpik'        => ['required', 'array', 'min:1'],
            'jenis_hpik.*'      => ['string'],
            'metode_pengujian'  => ['required', 'array', 'min:1'],
            'metode_pengujian.*'=> ['string'],
            'lab_uji'           => ['required', 'string', 'max:255'],
            'tw1'               => ['nullable', 'integer', 'min:0'],
            'tw2'               => ['nullable', 'integer', 'min:0'],
            'tw3'               => ['nullable', 'integer', 'min:0'],
            'tw4'               => ['nullable', 'integer', 'min:0'],
            'target_uji'        => ['nullable', 'integer', 'min:0'],
            'rencana_lokasi'    => ['nullable', 'string'],
            'rencana_jumlah_sampel'   => ['nullable', 'integer', 'min:0'],
            'rencana_metode_sampling' => ['nullable', 'string'],
            'kemampuan_uji_upt' => ['nullable', 'array'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'provinsi.required'        => 'Provinsi wajib diisi.',
            'kab_kota.required'        => 'Kabupaten/Kota wajib diisi.',
            'jenis_mp.required'        => 'Jenis Media Pembawa wajib dipilih.',
            'jenis_hpik.required'      => 'Minimal satu jenis HPIK harus dipilih.',
            'jenis_hpik.min'           => 'Minimal satu jenis HPIK harus dipilih.',
            'metode_pengujian.required'=> 'Minimal satu metode pengujian harus dipilih.',
            'metode_pengujian.min'     => 'Minimal satu metode pengujian harus dipilih.',
            'lab_uji.required'         => 'Lab Uji wajib diisi.',
        ];
    }
}
