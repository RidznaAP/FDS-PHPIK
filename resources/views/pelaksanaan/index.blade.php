<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Daftar Pelaksanaan Lapangan</title>
</head>
<body class="bg-light">
    <div class="container-fluid mt-4">
        <div class="card shadow">
            <div class="card-header bg-success text-white d-flex justify-content-between">
                <h5 class="mb-0">Daftar Realisasi Pelaksanaan Lapangan (Kolom 13-15)</h5>
                <a href="{{ route('perencanaan.index') }}" class="btn btn-light btn-sm">Kembali ke Perencanaan</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>No</th>
                                <th>Jenis MP</th>
                                <th>Lokasi Pengambilan (Kolom 13)</th>
                                <th>Jumlah Sampel (Kolom 14)</th>
                                <th>Metode (Kolom 15)</th>
                                <th>Koordinat (GIS)</th>
                                <th>Tanggal Input</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pelaksanaans as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->perencanaan->jenis_mp }}</td>
                                <td>{{ $item->lokasi_pengambilan_sampel }}</td>
                                <td class="text-center">{{ $item->jumlah_sampel }}</td>
                                <td class="text-center">{{ $item->metode_pengambilan_sampel }}</td>
                                <td class="small">
                                    <a href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}" target="_blank">
                                        {{ $item->latitude }}, {{ $item->longitude }}
                                    </a>
                                </td>
                                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Belum ada data pelaksanaan lapangan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>