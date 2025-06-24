<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Product; // Pastikan mengimpor Model Product
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $page = Page::where('slug', 'home')->first();

        if (!$page) {
            // Jika halaman 'home' tidak ditemukan, bisa tampilkan default atau redirect
            // Untuk memastikan setidaknya ada $page untuk view
            $page = new Page(['name' => 'Default Home', 'title' => 'Default Home Page']);
            $page->setRelation('sections', collect()); // Pastikan sections adalah Collection kosong
        }

        // Memproses setiap seksi untuk menambahkan data spesifik (misalnya, produk untuk product_grid)
        foreach ($page->sections as $section) {
            if ($section->section_type === 'product_grid') {
                $section->productsForGrid = $this->getProductsForGrid($section->content);
            }
            // Anda bisa menambahkan logika tambahan untuk tipe seksi lain jika perlu pre-load data
        }

        return view('template1.home', compact('page'));
    }

    public function showPage($slug)
    {
        $page = Page::where('slug', $slug)
                    ->where('is_active', true)
                    ->firstOrFail();

        // Memproses setiap seksi untuk menambahkan data spesifik (misalnya, produk untuk product_grid)
        foreach ($page->sections as $section) {
            if ($section->section_type === 'product_grid') {
                $section->productsForGrid = $this->getProductsForGrid($section->content);
            }
        }
        
        return view('template1.home', compact('page')); // Atau 'template1.page' jika Anda buat
    }

    /**
     * Helper method to get products based on product_grid section content.
     */
    private function getProductsForGrid(array $content)
    {
        $products = collect(); // Menginisialisasi koleksi kosong

        $filterType = $content['filter_type'] ?? 'latest';
        $limit = $content['display_limit'] ?? 8;

        switch ($filterType) {
            case 'latest':
                $products = Product::where('is_active', true)->latest()->limit($limit)->get();
                break;
            case 'featured':
                // Asumsi ada kolom 'is_featured' di tabel products
                $products = Product::where('is_active', true)->where('is_featured', true)->inRandomOrder()->limit($limit)->get();
                break;
            case 'bestsellers':
                // Ini akan membutuhkan logika yang lebih kompleks (misal, berdasarkan jumlah penjualan)
                // Untuk sementara, mungkin produk acak atau yang paling sering dilihat
                $products = Product::where('is_active', true)->inRandomOrder()->limit($limit)->get();
                break;
            case 'custom':
                $productIds = array_map('intval', $content['product_ids'] ?? []); // Pastikan ID adalah integer
                if (!empty($productIds)) {
                    $products = Product::where('is_active', true)->whereIn('id', $productIds)->get();
                    // Untuk mempertahankan urutan sesuai input ID, Anda bisa menggunakan sortBy:
                    // $products = $products->sortBy(function($product) use ($productIds) {
                    //     return array_search($product->id, $productIds);
                    // });
                }
                break;
            default:
                $products = Product::where('is_active', true)->latest()->limit($limit)->get();
                break;
        }

        return $products;
    }
}
