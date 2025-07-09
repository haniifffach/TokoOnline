@extends('backend.v_layouts.app')

@section('content')
<!-- Template -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <form class="form-horizontal" action="{{ route('backend.laporan.cetakproduk') }}" method="post" target="_blank">
                @csrf
                <div class="card-body">
                    <h4 class="card-title">{{ $judul }}</h4>

                    <div class="form-group">
                        <label for="tanggal_awal">Tanggal Awal</label>
                        <input type="date" 
                               id="tanggal_awal"
                               name="tanggal_awal" 
                               onkeypress="return hanyaAngka(event)" 
                               value="{{ old('tanggal_awal') }}" 
                               class="form-control @error('tanggal_awal') is-invalid @enderror" 
                               placeholder="Masukkan Tanggal Awal">
                        @error('tanggal_awal')
                            <span class="invalid-feedback alert-danger" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <input type="date" 
                               id="tanggal_akhir"
                               name="tanggal_akhir" 
                               onkeypress="return hanyaAngka(event)" 
                               value="{{ old('tanggal_akhir') }}" 
                               class="form-control @error('tanggal_akhir') is-invalid @enderror" 
                               placeholder="Masukkan Tanggal Akhir">
                        @error('tanggal_akhir')
                            <span class="invalid-feedback alert-danger" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <br>
                    <button type="submit" class="btn btn-primary">Cetak</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Template -->
@endsection