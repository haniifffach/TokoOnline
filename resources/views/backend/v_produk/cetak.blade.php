<style>
    table {
        border-collapse: collapse;
        width: 100%;
        border: 1px solid #ccc;
    }
    table th,
    table td {
        border: 1px solid #ccc;
        padding: 6px;
        font-weight: normal;
        text-align: left;
    }
    table th {
        background-color: #f2f2f2;
    }
</style>

<table>
    {{-- Header gambar, jika ingin digunakan silakan uncomment --}}
    {{-- 
    <tr>
        <td align="center">
            <img src="{{ asset('images/header.png') }}" width="50%">
        </td>
    </tr> 
    --}}
    <tr>
        <td>
            <strong>Perihal:</strong> {{ $judul }} <br>
            <strong>Tanggal:</strong> {{ $tanggalAwal }} s/d {{ $tanggalAkhir }}
        </td>
    </tr>
</table>

<p></p>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kategori</th>
            <th>Status</th>
            <th>Nama Produk</th>
            <th>Harga</th>
            <th>Stok</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cetak as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $row->kategori->nama_kategori }}</td>
                <td>
                    @if ($row->status == 1)
                        Publis
                    @elseif ($row->status == 0)
                        Blok
                    @endif
                </td>
                <td>{{ $row->nama_produk }}</td>
                <td>Rp. {{ number_format($row->harga, 0, ',', '.') }}</td>
                <td>{{ $row->stok }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    window.onload = function () {
        printStruk();
    };

    function printStruk() {
        window.print();
    }
</script>
