<!DOCTYPE html>
<html>
<head>
    <title>Laporan Hasil E-Monev KIP</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #dddddd; text-align: left; padding: 8px; font-size: 12px; }
        th { background-color: #f2f2f2; }
        h1 { text-align: center; }
        .info { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Laporan Hasil E-Monev KIP</h1>
    <div class="info">
        <p><strong>Klasifikasi:</strong> {{ $namaKlasifikasi }}</p>
        <p><strong>Tanggal Unduh:</strong> {{ $tanggal }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Badan Publik</th>
                <th>Jadwal</th>
                <th>Nilai Akhir</th>
                <th>Tanggal Verifikasi</th>
                <th>Klasifikasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporans as $laporan)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $laporan->user->badanPublik->nama_badan_publik ?? 'N/A' }}</td>
                    <td>{{ $laporan->jadwal->nama ?? '-' }}</td>
                    <td>{{ number_format($laporan->nilai_akhir, 2) }}</td>
                    <td>{{ $laporan->verified_at ? \Carbon\Carbon::parse($laporan->verified_at)->isoFormat('D MMM YYYY') : '-' }}</td>
                    <td>{{ $laporan->klasifikasiPenilaian->nama ?? 'Belum terklasifikasi' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
