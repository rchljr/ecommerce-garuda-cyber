<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\CustomTema; // Pastikan ini mengarah ke model tema Anda

class TemaComposer
{
    /**
     * Mengikat data ke view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $tenant = request()->get('tenant');
        $customTema = null;

        if ($tenant) {
            // Ambil data tema dari database tenant yang aktif
            $customTema = $tenant->execute(function () {
                // Ambil baris pertama dari data tema
                return CustomTema::first();
            });
        }

        // Kirim variabel $customTema ke view yang dituju
        $view->with('customTema', $customTema);
    }
}