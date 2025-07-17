<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\ShopSetting;
use App\Models\Hero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TemplateEditorController extends Controller
{
    // Method edit tetap sama, mengambil semua data
    public function edit()
    {
        $user = Auth::user();
        $shop = $user->shop;
        $hero = $shop->heroes()->where('is_active', true)->orderBy('order')->first();
        $settings = ShopSetting::where('shop_id', $shop->id)->pluck('value', 'key');
        return view('dashboard-mitra.editor.index', compact('settings', 'shop', 'hero'));
    }

    // Method baru khusus untuk menyimpan Hero
    public function updateHero(Request $request)
    {
        $user = Auth::user();
        $shop = $user->shop;

        $hero = $shop->heroes()->where('is_active', true)->orderBy('order')->firstOrNew(['shop_id' => $shop->id]);
        $hero->fill($request->only(['title', 'subtitle', 'description', 'button_text', 'button_url']));

        if ($request->hasFile('hero_image')) {
            if ($hero->image && Storage::disk('public')->exists($hero->image)) {
                Storage::disk('public')->delete($hero->image);
            }
            $hero->image = $request->file('hero_image')->store('hero_images/' . $shop->id, 'public');
        }
        $hero->save();

        return response()->json(['success' => true, 'message' => 'Pengaturan Hero berhasil disimpan!']);
    }

    // Method baru khusus untuk menyimpan Pengaturan Umum
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        $shop = $user->shop;

        foreach ($request->except(['_token']) as $key => $value) {
            if ($value !== null) {
                ShopSetting::updateOrCreate(
                    ['shop_id' => $shop->id, 'key' => $key],
                    ['value' => $value]
                );
            }
        }

        return response()->json(['success' => true, 'message' => 'Pengaturan Umum berhasil disimpan!']);
    }
    public function update(Request $request)
    {
        $request->validate([
            'selected_template' => 'required|string',
        ]);

        $user = Auth::user();
        $shop = $user->shop;

        // Simpan pilihan tema ke tabel ShopSetting
        ShopSetting::updateOrCreate(
            ['shop_id' => $shop->id, 'key' => 'template'],
            ['value' => $request->selected_template]
        );

        return redirect()->route('mitra.editor.edit')->with('success', 'Tema berhasil diperbarui.');
    }
}
