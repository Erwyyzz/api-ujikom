<!DOCTYPE html>
<html>
<head>
    <title>Laporan Peminjaman Alat</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { text-align: center; }
        .text-center { text-align: center; }
        .footer { margin-top: 20px; text-align: center; font-size: 12px; color: #888; }
        .badge-diajukan { background: #f59e0b; color: white; padding: 2px 8px; border-radius: 4px; }
        .badge-dipinjam { background: #3b82f6; color: white; padding: 2px 8px; border-radius: 4px; }
        .badge-dikembalikan { background: #10b981; color: white; padding: 2px 8px; border-radius: 4px; }
        .badge-telat { background: #ef4444; color: white; padding: 2px 8px; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Laporan Peminjaman Alat</h1>
    <p>Tanggal Cetak: {{ now()->format('d-m-Y H:i') }}</p>
    <p>Total Data: {{ $peminjaman->count() }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Peminjam</th>
                <th>Alat</th>
                <th>Jumlah</th>
                <th>Tgl Pinjam</th>
                <th>Rencana Kembali</th>
                <th>Status</th>
                <th>Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peminjaman as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->user->name ?? '-' }}</td>
                <td>
                    @foreach($item->detailPinjam as $detail)
                        {{ $detail->alat->nama_alat ?? 'Alat' }}<br>
                    @endforeach
                </td>
                <td>
                    @foreach($item->detailPinjam as $detail)
                        {{ $detail->jumlah }}<br>
                    @endforeach
                </td>
                <td>{{ $item->tgl_pinjam }}</td>
                <td>{{ $item->tgl_kembali_plan ?? '-' }}</td>
                <td>
                    <span class="badge-{{ $item->status }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </td>
                <td>
                    @if($item->pengembalian && $item->pengembalian->denda > 0)
                        Rp {{ number_format($item->pengembalian->denda, 0, ',', '.') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Belum ada data peminjaman.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d-m-Y H:i:s') }}
    </div>
</body>
</html>