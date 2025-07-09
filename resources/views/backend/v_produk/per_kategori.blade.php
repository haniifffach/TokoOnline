@extends('backend.v_layouts.app')

@section('title', 'Produk - ' . $kategori->nama_kategori)

@section('content')
<div class="container">
    <h4 class="mb-4">Produk Kategori: <strong>{{ $kategori->nama_kategori }}</strong></h4>

    @if($produk->isEmpty())
        <div class="alert alert-warning">Tidak ada produk dalam kategori ini.</div>
    @else
        <div class="row">
            @foreach($produk as $item)
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5>{{ $item->nama_produk }}</h5>
                            <p>Harga: Rp{{ number_format($item->harga, 0, ',', '.') }}</p>
                            {{-- Tambahkan detail lain sesuai kebutuhan --}}
                            <a href="{{ route('backend.produk.show', $item->id) }}" class="btn btn-sm btn-primary">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
