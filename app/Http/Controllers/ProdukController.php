<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\FotoProduk;
use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produk = Produk::orderBy('updated_at', 'desc')->get();
        return view('backend.v_produk.index', [
            'judul' => 'Data Produk',
            'index' => $produk
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategori = Kategori::orderBy('nama_kategori', 'asc')->get();
        return view('backend.v_produk.create', [
            'judul' => 'Tambah Produk',
            'kategori' => $kategori
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kategori_id' => 'required',
            'nama_produk' => 'required|max:255|unique:produk',
            'detail' => 'required',
            'harga' => 'required',
            'berat' => 'required',
            'stok' => 'required',
            'foto' => 'required|image|mimes:jpeg,jpg,png,gif|file|max:2048',
        ], $messages = [
            'foto.image' => 'Format gambar gunakan file dengan ekstensi jpeg, jpg, png, atau gif.',
            'foto.max' => 'Ukuran file gambar Maksimal adalah 1024 KB.'
        ]);

        $validatedData['user_id'] = auth()->id();
        $validatedData['status'] = 0;

        if ($request->file('foto')) {
            $file = $request->file('foto');
            $extension = $file->getClientOriginalExtension();
            $originalFileName = date('YmdHis') . '_' . uniqid() . '.' . $extension;
            $directory = 'storage/img-produk/';

            // Simpan gambar asli
            $fileName = ImageHelper::uploadAndResize($file, $directory, $originalFileName);
            $validatedData['foto'] = $fileName;

            // Create thumbnails
            $this->createThumbnails($file, $directory, $originalFileName);
        }

        Produk::create($validatedData);

        return redirect()->route('backend.produk.index')->with('success', 'Data berhasil tersimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $produk = Produk::with('gambar')->findOrFail($id);
        $kategori = Kategori::orderBy('nama_kategori', 'asc')->get();
        return view('backend.v_produk.show', [
            'judul' => 'Detail Produk',
            'show' => $produk,
            'kategori' => $kategori
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $produk = Produk::findOrFail($id);
        $kategori = Kategori::orderBy('nama_kategori', 'asc')->get();
        return view('backend.v_produk.edit', [
            'judul' => 'Ubah Produk',
            'edit' => $produk,
            'kategori' => $kategori
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $produk = Produk::findOrFail($id);
        $rules = [
            'nama_produk' => 'required|max:255|unique:produk,nama_produk,' . $id,
            'kategori_id' => 'required',
            'status' => 'required',
            'detail' => 'required',
            'harga' => 'required',
            'berat' => 'required',
            'stok' => 'required',
            'foto' => 'image|mimes:jpeg,jpg,png,gif|file|max:2048',
        ];

        $messages = [
            'foto.image' => 'Format gambar gunakan file dengan ekstensi jpeg, jpg, png, atau gif.',
            'foto.max' => 'Ukuran file gambar Maksimal adalah 2048 KB.'
        ];

        $validatedData['user_id'] = auth()->id();
        $validatedData = $request->validate($rules, $messages);

        if ($request->file('foto')) {
            $this->deleteOldImages($produk);
            $file = $request->file('foto');
            $extension = $file->getClientOriginalExtension();
            $originalFileName = date('YmdHis') . '_' . uniqid() . '.' . $extension;
            $directory = 'storage/img-produk/';

            // Simpan gambar asli
            $fileName = ImageHelper::uploadAndResize($file, $directory, $originalFileName);
            $validatedData['foto'] = $fileName;

            // Create thumbnails
            $this->createThumbnails($file, $directory, $originalFileName);
        }

        $produk->update($validatedData);
        return redirect()->route('backend.produk.index')->with('success', 'Data berhasil diperbaharui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        $directory = public_path('storage/img-produk/');

        if ($produk->foto) {
            $this->deleteOldImages($produk);
        }

        // Hapus foto produk lainnya
        $fotoProduks = FotoProduk::where('produk_id', $id)->get();
        foreach ($fotoProduks as $fotoProduk) {
            $fotoPath = $directory . $fotoProduk->foto;
            if (file_exists($fotoPath)) {
                unlink($fotoPath);
            }
            $fotoProduk->delete();
        }

        $produk->delete();
        return redirect()->route('backend.produk.index')->with('success', 'Data berhasil dihapus');
    }

    // Method untuk menyimpan foto tambahan
    public function storeFoto(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'foto_produk.*' => 'image|mimes:jpeg,jpg,png,gif|file|max:1024',
        ]);

        if ($request->hasFile('foto_produk')) {
            foreach ($request->file('foto_produk') as $file) {
                $extension = $file->getClientOriginalExtension();
                $filename = date('YmdHis') . '_' . uniqid() . '.' . $extension;
                $directory = 'storage/img-produk/';

                // Simpan dan resize gambar menggunakan ImageHelper
                ImageHelper::uploadAndResize($file, $directory, $filename, 800, null);

                // Simpan data ke database
                FotoProduk::create([
                    'produk_id' => $request->produk_id,
                    'foto' => $filename,
                ]);
            }
        }

        return redirect()->route('backend.produk.show', $request->produk_id)
                         ->with('success', 'Foto berhasil ditambahkan.');
    }

    // Method untuk menghapus foto
    public function destroyFoto($id)
    {
        $foto = FotoProduk::findOrFail($id);
        $produkId = $foto->produk_id;

        $imagePath = public_path('storage/img-produk/') . $foto->foto;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        $foto->delete();
        return redirect()->route('backend.produk.show', $produkId)
                         ->with('success', 'Foto berhasil dihapus.');
    }

    // Method untuk Form Laporan Produk
    public function formProduk()
    {
        return view('backend.v_produk.form', ['judul' => 'Laporan Data Produk',
        ]);
    }

    // Method untuk Cetak Laporan Produk
    public function cetakProduk(Request $request)
    {
        $request->validate([
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ], [
            'tanggal_awal.required' => 'Tanggal Awal harus diisi.',
            'tanggal_akhir.required' => 'Tanggal Akhir harus diisi.',
            'tanggal_akhir.after_or_equal' => 'Tanggal Akhir harus lebih besar atau sama dengan Tanggal Awal.',
        ]);

        $tanggalAwal = Carbon::parse($request->input('tanggal_awal'))->startOfDay();
        $tanggalAkhir = Carbon::parse($request->input('tanggal_akhir'))->endOfDay();

        $produk = Produk::whereBetween('updated_at', [$tanggalAwal, $tanggalAkhir])
                        ->orderBy('id', 'desc')
                        ->get();

        return view('backend.v_produk.cetak', [
            'judul' => 'Laporan Produk',
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
            'cetak' => $produk
        ]);
    }

    public function laporan()
{
    $laporan = DB::table('transaksi_detail')
        ->join('produk', 'transaksi_detail.produk_id', '=', 'produk.id')
        ->select('produk.nama', DB::raw('SUM(transaksi_detail.jumlah) as total_terjual'), DB::raw('SUM(transaksi_detail.harga * transaksi_detail.jumlah) as total_pendapatan'))
        ->groupBy('produk.id', 'produk.nama')
        ->orderByDesc('total_terjual')
        ->get();

    return view('v_produk.laporan', compact('laporan'));
}

public function cetakLaporan()
{
    $laporan = DB::table('transaksi_detail')
        ->join('produk', 'transaksi_detail.produk_id', '=', 'produk.id')
        ->select('produk.nama', DB::raw('SUM(transaksi_detail.jumlah) as total_terjual'), DB::raw('SUM(transaksi_detail.harga * transaksi_detail.jumlah) as total_pendapatan'))
        ->groupBy('produk.id', 'produk.nama')
        ->orderByDesc('total_terjual')
        ->get();

    return view('v_produk.cetak_laporan', compact('laporan'));
}

    // Helper untuk membuat thumbnail
    private function createThumbnails($file, $directory, $originalFileName)
    {
        // Create thumbnail 1 (lg)
        $thumbnailLg = 'thumb_lg_' . $originalFileName;
        ImageHelper::uploadAndResize($file, $directory, $thumbnailLg, 800, null);

        // Create thumbnail 2 (md)
        $thumbnailMd = 'thumb_md_' . $originalFileName;
        ImageHelper::uploadAndResize($file, $directory, $thumbnailMd, 500, 519);

        // Create thumbnail 3 (sm)
        $thumbnailSm = 'thumb_sm_' . $originalFileName;
        ImageHelper::uploadAndResize($file, $directory, $thumbnailSm, 100, 110);
    }

    // Helper untuk menghapus gambar lama
    private function deleteOldImages($produk)
    {
        $directory = public_path('storage/img-produk/');
        
        if ($produk->foto) {
            $oldImagePath = $directory . $produk->foto;
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }

            // Hapus thumbnails
            $this->deleteThumbnail($produk->foto, 'thumb_lg');
            $this->deleteThumbnail($produk->foto, 'thumb_md');
            $this->deleteThumbnail($produk->foto, 'thumb_sm');
        }
    }

    // Helper untuk menghapus thumbnail
    private function deleteThumbnail($foto, $prefix)
    {
        $thumbnailPath = public_path('storage/img-produk/') . $prefix . '_' . $foto;
        if (file_exists($thumbnailPath)) {
            unlink($thumbnailPath);
        }
    }
}
