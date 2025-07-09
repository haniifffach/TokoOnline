@extends('v_layouts.app')

@section('content')
<div class="container mt-4">
    <h4>Laporan Penjualan Produk</h4>

    <!-- Tombol Cetak -->
    <a href="{{ route('produk.laporan.cetak') }}" class="btn btn-primary mb-3" target="_blank">Cetak</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Total Terjual</th>
                <th>Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->total_terjual }}</td>
                <td>Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
