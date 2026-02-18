<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Perencanaan HPIK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; }
        .card-header { background-color: #003366; color: white; }
    </style>
</head>
<body>
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header">
                        <h4 class="mb-0">Form Perencanaan Pemantauan HPIK</h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('perencanaan.store') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Provinsi</label>
                                    <input type="text" name="provinsi" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Kabupaten/Kota</label>
                                    <input type="text" name="kab_kota" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Jenis MP (Media Pembawa)</label>
                                    <input type="text" name="jenis_mp" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Jenis HPIK (Target)</label>
                                    <input type="text" name="jenis_hpik" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Kemampuan Uji UPT</label>
                                    <input type="text" name="kemampuan_uji_upt" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Metode Pengujian</label>
                                    <input type="text" name="metode_pengujian" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Lab Uji</label>
                                    <input type="text" name="lab_uji" class="form-control" required>
                                </div>
                                <div class="col-md-12 border-top pt-3 mt-4">
                                    <h6 class="text-primary fw-bold">Target Per Kuartal (TW)</h6>
                                    <div class="row g-2 text-center">
                                        <div class="col-3"><label>TW 1</label><input type="number" name="tw1" class="form-control" value="0"></div>
                                        <div class="col-3"><label>TW 2</label><input type="number" name="tw2" class="form-control" value="0"></div>
                                        <div class="col-3"><label>TW 3</label><input type="number" name="tw3" class="form-control" value="0"></div>
                                        <div class="col-3"><label>TW 4</label><input type="number" name="tw4" class="form-control" value="0"></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Total Target Uji (Tahun)</label>
                                    <input type="number" name="target_uji" class="form-control" required>
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">SIMPAN DATA PERENCANAAN</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
