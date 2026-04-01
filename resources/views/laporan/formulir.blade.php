<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Hasil Pemantauan HPIK</title>
    <style>
        @page { size: landscape; margin: 1cm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11px; line-height: 1.4; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 10px; font-weight: bold; }
        .info-table td { padding: 2px 0; }
        .main-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .main-table th, .main-table td { border: 1px solid black; padding: 4px; text-align: center; vertical-align: middle; }
        .main-table th { background: #f2f2f2; font-size: 9px; text-transform: uppercase; }
        .main-table thead tr:nth-child(2) th { font-size: 8px; }
        .footer { margin-top: 30px; width: 100%; }
        .footer-table { width: 100%; }
        .footer-table td { text-align: center; }
        .notes { margin-top: 20px; font-size: 10px; }
        .notes ol { padding-left: 20px; }
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="background:#f4f7fb; padding:10px; margin-bottom:20px; border-bottom:1px solid #ddd; text-align:center;">
        <button onclick="window.print()" style="padding:8px 20px; background:#206bc4; color:white; border:none; border-radius:4px; cursor:pointer; font-weight:bold;">
            🖨️ CETAK FORMULIR (PDF)
        </button>
        <p style="margin:5px 0 0 0; font-size:12px; color:#666;">Gunakan layout <b>Landscape</b> dan <b>Scale 100%</b> atau <b>Fit to Page</b> di pengaturan print.</p>
    </div>

    <div class="header">
        <div style="text-align: left; font-weight: bold; font-size: 14px;">Formulir Hasil Pemantauan HPIK</div>
        <h2 style="margin-top: 10px;">HASIL PEMANTAUAN HAMA DAN PENYAKIT IKAN KARANTINA TAHUN {{ $tahun }}</h2>
    </div>

    <table class="info-table">
        <tr>
            <td width="100">Periode</td>
            <td width="10">:</td>
            <td>___________________________</td>
        </tr>
        <tr>
            <td>Nama UPT</td>
            <td>:</td>
            <td>{{ $namaUpt }}</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="2" align="center" width="20">No</th>
                <th rowspan="2" align="center" width="120">Lokasi<br>Pemantauan<br>(Prop/Kab/Kec.)</th>
                <th rowspan="2" align="center" width="60">Tanggal<br>Pemantauan</th>
                <th colspan="7" align="center">Contoh Uji</th>
                <th colspan="4" align="center">Hasil Pemeriksaan</th>
                <th rowspan="2" align="center" width="40">Prev.</th>
                <th rowspan="2" align="center" width="40">Insidensi</th>
                <th rowspan="2" align="center" width="40">Lab.<br>Uji</th>
                <th rowspan="2" align="center" width="40">Ket</th>
            </tr>
            <tr>
                <th align="center" width="60">Jenis</th>
                <th align="center" width="40">Panjang<br>(cm)</th>
                <th align="center" width="40">Berat<br>(gram)</th>
                <th align="center" width="50">Asal<br>Benih/<br>Induk</th>
                <th align="center" width="40">Padat<br>Tebar</th>
                <th align="center" width="60">Gejala<br>klinis</th>
                <th align="center" width="40">Jumlah<br>Kematian</th>
                <th align="center" width="30">Parasit</th>
                <th align="center" width="30">Bakteri</th>
                <th align="center" width="30">Virus</th>
                <th align="center" width="30">Jamur</th>
            </tr>
            <tr style="background:#eee; font-size:8px;">
                @for($i=1; $i<=18; $i++) <th align="center">{{ $i }}</th> @endfor
            </tr>
        </thead>
        <tbody>
            @forelse($items as $idx => $item)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td style="text-align: left;">
                    {{ $item->perencanaan->provinsi ?? '-' }},<br>
                    {{ $item->perencanaan->kab_kota ?? '-' }},<br>
                    {{ $item->lokasi_pengambilan_sampel }}
                </td>
                <td>{{ $item->tanggal_pemantauan ? $item->tanggal_pemantauan->format('d/m/Y') : '-' }}</td>
                <td style="text-align: left;">
                    <b>{{ $item->jenis_ikan }}</b>
                    @if($item->nama_latin)<br><i>({{ $item->nama_latin }})</i>@endif
                </td>
                <td>{{ $item->panjang_cm ?? '-' }}</td>
                <td>{{ $item->berat_gram ?? '-' }}</td>
                <td>{{ $item->asal_benih_induk ?? '-' }}</td>
                <td>{{ $item->padat_tebar ?? '-' }}</td>
                <td>{{ $item->gejala_klinis ?? '-' }}</td>
                <td>{{ $item->jumlah_kematian ?? '-' }}</td>
                
                {{-- Hasil Pemeriksaan --}}
                <td>{{ $item->laboratorium->hasil_parasit ?? 'NT' }}</td>
                <td>{{ $item->laboratorium->hasil_bakteri ?? 'NT' }}</td>
                <td>{{ $item->laboratorium->hasil_virus   ?? 'NT' }}</td>
                <td>{{ $item->laboratorium->hasil_jamur    ?? 'NT' }}</td>

                <td>{{ $item->laboratorium->prevalensi ?? '-' }}</td>
                <td>{{ $item->laboratorium->insidensi ?? '-' }}</td>
                <td>{{ $item->laboratorium->lab_penguji ?? ($item->perencanaan->lab_uji ?? '-') }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
            </tr>
            @empty
            @for($row=0; $row<3; $row++)
            <tr>
                @for($col=0; $col<18; $col++) <td>&nbsp;</td> @endfor
            </tr>
            @endfor
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td width="60%"></td>
                <td>
                    Mengetahui,<br>Kepala UPT<br><br><br><br>
                    (..............................)
                </td>
            </tr>
        </table>
    </div>

    <div class="notes">
        <b>Catatan:</b>
        <ol>
            <li>Pengisian jenis (kolom 4) berupa nama lokal dan nama latin media pembawa, sedangkan hasil pemeriksaan (kolom 11, 12, 13, 14) berupa positif (+)/negatif (-) HPIK target;</li>
            <li>Pengisian lokasi (kolom 2) berupa nama provinsi, kabupaten/kota, kecamatan, dan desa;</li>
            <li>Form ini merupakan format laporan awal hasil Pemantauan yang disampaikan kepada BBKHIT;</li>
            <li>Jika pemeriksaan satu jenis ikan tidak dapat dilakukan untuk semua target HPIK agar disertai dengan alasannya;</li>
            <li>Untuk contoh uji yang terinfeksi HPIK dengan mencantumkan keterangan daerah distribusi, serta melampirkan gambar/foto (ikan yang terinfeksi, kolam/tambak, pathogen/hasil pemeriksaan laboratorium);</li>
        </ol>
    </div>

    <script>
        // Auto trigger print if wanted, but button is safer
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
