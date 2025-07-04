<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use App\Models\Hero;   
use App\Models\Banner; 
use App\Models\Product;

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

        $heroes = Hero::where('is_active', true)->orderBy('order')->get();
        $banners = Banner::where('is_active', true)->orderBy('order')->get();

        $bestSellers = Product::where('is_best_seller', true)
            ->where('status', 'active')
            ->latest()
            ->limit(8)
            ->get();

        $newArrivals = Product::where('is_new_arrival', true)
            ->where('status', 'active')
            ->latest()
            ->limit(8)
            ->get();

        $hotSales = Product::where('is_hot_sale', true)
            ->where('status', 'active')
            ->latest()
            ->limit(8)
            ->get();

        return view($template->path . '.home', compact(
            'template', 
            'heroes',
            'banners',
            'bestSellers',
            'newArrivals',
            'hotSales'
        ));
    }
}
