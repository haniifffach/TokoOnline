<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\Produk;

class BerandaController extends Controller
{
    /**
     * Halaman Beranda Backend
     * Menampilkan chart produk per kategori.
     */
    public function index()
    {
        // Ambil semua kategori dan hitung jumlah produk per kategori
        $kategori = Kategori::withCount('produk')->get();

        // Ambil nama kategori dan jumlah produknya
        $labels = $kategori->pluck('nama_kategori')->toArray(); // sesuaikan dengan kolom di DB
        $jumlah = $kategori->pluck('produk_count')->toArray();  // hasil from withCount

        return view('backend.v_beranda.index', [
            'judul'   => 'Halaman Beranda',
            'kategori' => $kategori,
            'labels'  => $labels,
            'jumlah'  => $jumlah,
        ]);
    }

    /**
     * Menampilkan produk berdasarkan kategori yang diklik dari chart
     */
    public function produkPerKategori($namaKategori)
    {
        // Ambil kategori berdasarkan nama_kategori
        $kategori = Kategori::where('nama_kategori', $namaKategori)->firstOrFail();

        // Ambil produk berdasarkan kategori tersebut
        $produk = Produk::where('kategori_id', $kategori->id)->get();

        return view('backend.v_produk.per_kategori', [
            'kategori' => $kategori,
            'produk'   => $produk,
        ]);
    }
}
