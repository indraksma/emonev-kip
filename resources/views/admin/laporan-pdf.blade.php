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
        <p><strong>Kategori:</strong> {{ $namaKategori }}</p>
        <p><strong>Tanggal Unduh:</strong> {{ $tanggal }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Badan Publik</th>
                <th>Kategori Kuesioner</th>
                <th>Nilai Akhir</th>
                {{-- KOLOM BARU DITAMBAHKAN --}}
                <th>Tanggal Submit</th>
                <th>Predikat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporans as $laporan)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $laporan->submission->user->badanPublik->nama_badan_publik ?? 'N/A' }}</td>
                    <td>{{ $laporan->submission->kategori->nama ?? 'N/A' }}</td>
                    <td>{{ $laporan->nilai }}</td>
                    {{-- DATA BARU DITAMBAHKAN --}}
                    <td>{{ \Carbon\Carbon::parse($laporan->submission->tanggal_submit)->isoFormat('D MMM YYYY') }}</td>
                    <td>{{ $laporan->status_informatif }}</td>
                </tr>
            @empty
                <tr>
                    {{-- Colspan disesuaikan menjadi 6 --}}
                    <td colspan="6" style="text-align: center;">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
