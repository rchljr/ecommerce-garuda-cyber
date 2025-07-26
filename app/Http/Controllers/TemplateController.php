<?php

namespace App\Http\Controllers;

use App\Models\Hero;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemplateController extends Controller
{
    /**
     * Menampilkan preview dari sebuah template.
     *
     * @param  \App\Models\Template  $template
     * @return \Illuminate\View\View
     */
    public function preview(Template $template)
    {
        // Untuk mode preview, kita tidak mengambil data asli dari database.
        // Kita mengirimkan collection kosong agar view dapat menampilkan
        // state default atau @empty-nya.
        $emptyCollection = collect();

        // Kirim semua data yang diperlukan ke tampilan
        return view($template->path . '.home', [
            'template' => $template,
            'heroes' => $emptyCollection,
            'banners' => $emptyCollection,
            'bestSellers' => $emptyCollection,
            'newArrivals' => $emptyCollection,
            'hotSales' => $emptyCollection,
            'currentShop' => null, // Preview tidak memiliki shop context
            'isPreview' => true,
        ]);
    }
}
