<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Perencanaan HPIK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-custom th { background-color: #003366 !important; color: white; vertical-align: middle; font-size: 0.85rem; }
        .table-custom td { font-size: 0.85rem; vertical-align: middle; }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid mt-4">
        <div class="card shadow">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="fw-bold mb-0 text-primary">Daftar Perencanaan Pemantauan HPIK</h5>
                <a href="{{ route('perencanaan.create') }}" class="btn btn-primary btn-sm">Tambah Perencanaan Baru</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center align-middle table-custom">
                        <thead>
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Provinsi</th>
                                <th rowspan="2">Kabupaten / Kota</th>
                                <th rowspan="2">Jenis MP</th>
                                <th rowspan="2">Jenis HPIK</th>
                                <th rowspan="2">Kemampuan Uji UPT</th>
                                <th rowspan="2">Metode Pengujian</th>
                                <th rowspan="2">Lab Uji</th>
                                <th rowspan="2">Target Uji</th>
                                <th colspan="4">Waktu Pelaksanaan</th>
                                <th rowspan="2">Total Pengujian</th>
                                <th rowspan="2">Aksi</th>
                            </tr>
                            <tr>
                                <th>TW 1</th>
                                <th>TW 2</th>
                                <th>TW 3</th>
                                <th>TW 4</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($perencanaans as $key => $p)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $p->provinsi }}</td>
                                <td>{{ $p->kab_kota }}</td>
                                <td>{{ $p->jenis_mp }}</td>
                                <td>{{ $p->jenis_hpik }}</td>
                                <td>{{ $p->kemampuan_uji_upt }}</td>
                                <td>{{ $p->metode_pengujian }}</td>
                                <td>{{ $p->lab_uji }}</td>
                                <td>{{ $p->target_uji }}</td>
                                <td>{{ $p->tw1 }}</td>
                                <td>{{ $p->tw2 }}</td>
                                <td>{{ $p->tw3 }}</td>
                                <td>{{ $p->tw4 }}</td>
                                <td class="fw-bold text-primary">{{ $p->total_pengujian }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-warning btn-sm">Edit</button>
                                        <button class="btn btn-danger btn-sm">Hapus</button><a href="{{ url('/pelaksanaan/tambah/'.$p->id) }}" class="btn btn-success btn-sm">
   Input Pelaksanaan
</a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="15" class="text-muted italic">Belum ada data perencanaan.</td>
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
