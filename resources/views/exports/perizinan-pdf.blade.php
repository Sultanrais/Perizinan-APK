<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Perizinan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            padding: 0;
            font-size: 18px;
        }
        .info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: right;
            font-size: 10px;
        }
        .status-badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 11px;
        }
        .status-pending {
            background-color: #ffeeba;
            color: #856404;
        }
        .status-disetujui {
            background-color: #c3e6cb;
            color: #155724;
        }
        .status-ditolak {
            background-color: #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PERIZINAN</h1>
        <p>Periode: {{ $startDate }} - {{ $endDate }}</p>
    </div>

    <div class="info">
        <table style="width: auto; border: none;">
            <tr>
                <td style="border: none;">Tanggal Cetak</td>
                <td style="border: none;">: {{ $tanggal_cetak }}</td>
            </tr>
            <tr>
                <td style="border: none;">Status</td>
                <td style="border: none;">: {{ $status }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>No. Izin</th>
                <th>Nama Pemohon</th>
                <th>NIK</th>
                <th>Jenis Usaha</th>
                <th>Status</th>
                <th>Tanggal Pengajuan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($perizinans as $index => $perizinan)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $perizinan->nomor_izin }}</td>
                <td>{{ $perizinan->nama_pemohon }}</td>
                <td>{{ $perizinan->nik }}</td>
                <td>{{ $perizinan->jenis_usaha }}</td>
                <td>
                    <span class="status-badge status-{{ $perizinan->status }}">
                        {{ ucfirst($perizinan->status) }}
                    </span>
                </td>
                <td>{{ $perizinan->tanggal_pengajuan ? \Carbon\Carbon::parse($perizinan->tanggal_pengajuan)->format('d/m/Y') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">Tidak ada data perizinan</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ $tanggal_cetak }}
    </div>
</body>
</html>
