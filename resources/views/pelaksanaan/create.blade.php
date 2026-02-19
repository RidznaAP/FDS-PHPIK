<!DOCTYPE html>
<html lang="id">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Input Pelaksanaan Lapangan</title>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Tahap Pelaksanaan: {{ $rencana->jenis_mp }} ({{ $rencana->kab_kota }})</h5>
            </div>
            <div class="card-body">
                <form action="{{ url('/pelaksanaan/simpan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="perencanaan_id" value="{{ $rencana->id }}">

                    <div class="mb-3">
                        <label>Lokasi Pengambilan Sampel (Kolom 13)</label>
                        <input type="text" name="lokasi_pengambilan_sampel" class="form-control" placeholder="Contoh: Tambak Rakyat Desa X" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Jumlah Sampel (Kolom 14)</label>
                            <input type="number" name="jumlah_sampel" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Metode Pengambilan (Kolom 15)</label>
                            <select name="metode_pengambilan_sampel" class="form-control">
                                <option value="Acak">Acak</option>
                                <option value="Selektif">Selektif</option>
                            </select>
                        </div>
                    </div>

                    <div class="row bg-warning bg-opacity-10 p-3 rounded">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Koordinat Lokasi</h6>
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="getLocation()">
                                📍 Ambil Lokasi GPS
                            </button>
                        </div>
                        <div id="geo-status" class="mb-2" style="display:none;"></div>
                        <div class="col-md-6">
                            <label>Latitude</label>
                            <input type="text" name="latitude" id="lat" class="form-control" placeholder="Contoh: -5.1234567">
                        </div>
                        <div class="col-md-6">
                            <label>Longitude</label>
                            <input type="text" name="longitude" id="lng" class="form-control" placeholder="Contoh: 119.4567890">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-4">Simpan Hasil Lapangan</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function getLocation() {
            var statusEl = document.getElementById('geo-status');
            statusEl.style.display = 'block';

            if (!navigator.geolocation) {
                statusEl.innerHTML = '<div class="alert alert-danger py-1">Browser tidak mendukung GPS. Silakan isi manual.</div>';
                return;
            }

            statusEl.innerHTML = '<div class="alert alert-info py-1">⏳ Mengambil lokasi GPS...</div>';

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    document.getElementById("lat").value = position.coords.latitude;
                    document.getElementById("lng").value = position.coords.longitude;
                    statusEl.innerHTML = '<div class="alert alert-success py-1">✅ Lokasi berhasil diambil!</div>';
                },
                function(error) {
                    var msg = 'Gagal mengambil lokasi. ';
                    if (error.code === 1) msg += 'Izin lokasi ditolak.';
                    else if (error.code === 2) msg += 'Lokasi tidak tersedia.';
                    else if (error.code === 3) msg += 'Waktu habis.';
                    msg += ' Silakan isi koordinat secara manual.';
                    statusEl.innerHTML = '<div class="alert alert-warning py-1">⚠️ ' + msg + '</div>';
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }
    </script>
</body>
</html>
