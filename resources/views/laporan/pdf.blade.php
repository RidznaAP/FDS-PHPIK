<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan HPIK — {{ $filterWilayah }} ({{ $filterTahun }})</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; font-size: 11px; color: #1e293b; background: #fff; }

        .print-header {
            background: linear-gradient(135deg, #0a1628, #1565c0);
            color: white;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .print-header h1 { font-size: 16px; font-weight: 700; }
        .print-header p  { font-size: 10px; opacity: .7; margin-top: 3px; }
        .logo { font-size: 2rem; }

        .stats-row {
            display: flex; gap: 12px; margin-bottom: 20px;
        }
        .stat-box {
            flex: 1; border: 1px solid #e2e8f0; border-radius: 8px;
            padding: 12px 16px; text-align: center;
        }
        .stat-num { font-size: 2rem; font-weight: 700; }
        .stat-lbl { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: .5px; }

        .section-title {
            font-size: 12px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .5px; color: #475569; margin-bottom: 8px;
            padding-bottom: 4px; border-bottom: 2px solid #e2e8f0;
        }

        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        thead tr { background: #0a1628; color: white; }
        th { padding: 7px 8px; text-align: left; font-weight: 600; }
        td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        tr:nth-child(even) td { background: #f8fafc; }
        tr:hover td { background: #f0f9ff; }

        .badge {
            display: inline-block; padding: 2px 7px; border-radius: 4px;
            font-size: 9px; font-weight: 600;
        }
        .badge-draft    { background: #f1f5f9; color: #475569; }
        .badge-waiting  { background: #fef9c3; color: #ca8a04; }
        .badge-approved { background: #dcfce7; color: #16a34a; }

        .print-footer {
            margin-top: 20px; padding-top: 12px; border-top: 1px solid #e2e8f0;
            display: flex; justify-content: space-between; color: #94a3b8; font-size: 9px;
        }

        .no-print { text-align: center; padding: 16px; }
        .btn-print { background: #0d6efd; color: white; border: none; padding: 10px 24px; border-radius: 6px; cursor: pointer; font-size: 14px; }
        .btn-back  { background: #6c757d; color: white; border: none; padding: 10px 16px; border-radius: 6px; cursor: pointer; font-size: 14px; margin-right: 8px; }

        @media print {
            .no-print { display: none; }
            body { background: #fff; }
        }
    </style>
</head>
<body>

{{-- Print / Back buttons (hidden on print) --}}
<div class="no-print">
    <button class="btn-back" onclick="history.back()">← Kembali</button>
    <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
    <p style="margin-top:8px;color:#64748b;font-size:12px;">Gunakan Ctrl+P → "Save as PDF" untuk ekspor PDF.</p>
</div>

{{-- Header --}}
<div class="print-header">
    <div>
        <div style="font-size:10px;opacity:.6;margin-bottom:4px;">KEMENTERIAN KELAUTAN DAN PERIKANAN</div>
        <h1>🐟 Laporan Pemantauan HPIK</h1>
        <p>
            Wilayah: <strong>{{ $filterWilayah }}</strong> &nbsp;|&nbsp;
            Tahun: <strong>{{ $filterTahun }}</strong> &nbsp;|&nbsp;
            Tanggal Cetak: {{ date('d F Y') }}
        </p>
    </div>
</div>

{{-- Stats --}}
<div class="stats-row">
    <div class="stat-box">
        <div class="stat-num" style="color:#2563eb;">{{ $totalPerencanaan }}</div>
        <div class="stat-lbl">Total Perencanaan</div>
    </div>
    <div class="stat-box">
        <div class="stat-num" style="color:#16a34a;">{{ $totalPelaksanaan }}</div>
        <div class="stat-lbl">Pelaksanaan Lapangan</div>
    </div>
    <div class="stat-box">
        <div class="stat-num" style="color:#7c3aed;">{{ $labDone }}</div>
        <div class="stat-lbl">Hasil Uji Lab</div>
    </div>
    <div class="stat-box">
        <div class="stat-num" style="color:#ca8a04;">{{ $perencanaans->where('status','approved')->count() }}</div>
        <div class="stat-lbl">Disetujui</div>
    </div>
</div>

{{-- Table --}}
<div class="section-title">Daftar Perencanaan Pemantauan</div>
<table>
    <thead>
        <tr>
            <th style="width:28px">No</th>
            <th>Provinsi / Kab-Kota</th>
            <th>Komoditas</th>
            <th>HPIK Target</th>
            <th>BKHIT</th>
            <th>Tahun</th>
            <th>Lapangan</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($perencanaans as $i => $p)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $p->provinsi }}, {{ $p->kab_kota }}</td>
            <td>{{ $p->jenis_mp }}</td>
            <td>{{ $p->jenis_hpik }}</td>
            <td>{{ $p->user->upt_asal ?? $p->user->name ?? '-' }}</td>
            <td>{{ $p->created_at->format('Y') }}</td>
            <td>{{ $p->pelaksanaans->count() }}x</td>
            <td>
                <span class="badge badge-{{ $p->status }}">
                    {{ strtoupper($p->status) }}
                </span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" style="text-align:center;padding:20px;color:#94a3b8;">Tidak ada data</td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- Footer --}}
<div class="print-footer">
    <span>SIP-HPIK — Sistem Informasi Pemantauan HPIK &copy; {{ date('Y') }}</span>
    <span>Dicetak: {{ date('d/m/Y H:i') }}</span>
</div>

</body>
</html>
