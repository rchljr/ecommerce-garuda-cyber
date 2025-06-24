<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PageSectionController extends Controller
{
    // Menampilkan seksi-seksi untuk halaman tertentu
    public function index(Page $page)
    {
        $pageSections = $page->sections; // Ambil seksi untuk halaman ini
        return view('dashboard-mitra.page_sections.index', compact('page', 'pageSections'));
    }

    // Menampilkan form untuk membuat seksi baru
    public function create(Page $page)
    {
        // Anda mungkin perlu daftar tipe seksi yang tersedia di sini
        $sectionTypes = [
            'hero' => 'Hero Section',
            'banner' => 'Banner Section',
            'product_grid' => 'Product Grid Section',
            'blog_latest' => 'Latest Blog Section',
            'text_block' => 'Text Block',
            // ... tambahkan tipe seksi lainnya
        ];
        return view('dashboard-mitra.page_sections.create', compact('page', 'sectionTypes'));
    }

    // Menyimpan seksi baru
    public function store(Request $request, Page $page)
    {
        // Aturan validasi umum
        $rules = [
            'section_type' => 'required|string',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ];

        // Aturan validasi kondisional berdasarkan section_type
        if ($request->input('section_type') === 'hero') {
            $rules = array_merge($rules, [
                'content.slides' => 'required|array',
                'content.slides.*.id' => 'nullable|string',
                'content.slides.*.title' => 'required|string|max:255',
                'content.slides.*.subtitle' => 'nullable|string|max:255',
                'content.slides.*.description' => 'nullable|string',
                'content.slides.*.button_text' => 'nullable|string|max:255',
                'content.slides.*.button_url' => 'nullable|string|max:255',
                'content.slides.*.background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'content.slides.*.current_background_image' => 'nullable|string',
            ]);
        } elseif ($request->input('section_type') === 'banner') {
            // Validasi untuk 3 banner
            $rules = array_merge($rules, [
                'content.title_1' => 'nullable|string|max:255',
                'content.url_1' => 'nullable|string|max:255',
                'content.button_text_1' => 'nullable|string|max:255',
                'content.image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'content.current_image_1' => 'nullable|string',

                'content.title_2' => 'nullable|string|max:255',
                'content.url_2' => 'nullable|string|max:255',
                'content.button_text_2' => 'nullable|string|max:255',
                'content.image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'content.current_image_2' => 'nullable|string',

                'content.title_3' => 'nullable|string|max:255',
                'content.url_3' => 'nullable|string|max:255',
                'content.button_text_3' => 'nullable|string|max:255',
                'content.image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'content.current_image_3' => 'nullable|string',
            ]);
        } elseif ($request->input('section_type') === 'text_block') {
            $rules = array_merge($rules, [
                'content.heading' => 'nullable|string|max:255',
                'content.body' => 'nullable|string',
            ]);
        }
        // ... tambahkan kondisi untuk tipe seksi lainnya

        $request->validate($rules); // Jalankan validasi

        $content = $this->processSectionContent($request, $request->input('section_type'));

        $page->sections()->create([
            'section_type' => $request->input('section_type'),
            'order' => $request->input('order', 0),
            'is_active' => $request->boolean('is_active'),
            'content' => $content,
        ]);

        return redirect()->route('mitra.pages.sections.index', $page)->with('success', 'Section added successfully!');
    }

    // Menampilkan form untuk mengedit seksi
    public function edit(Page $page, PageSection $section)
    {
        // Sama seperti create, Anda mungkin perlu daftar tipe seksi
        $sectionTypes = [
            'hero' => 'Hero Section', 'banner' => 'Banner Section', 'product_grid' => 'Product Grid Section',
            'blog_latest' => 'Latest Blog Section', 'text_block' => 'Text Block',
        ];
        return view('dashboard-mitra.page_sections.edit', compact('page', 'section', 'sectionTypes'));
    }

    // Mengupdate seksi
   public function update(Request $request, Page $page, PageSection $section)
    {
        // Aturan validasi umum
        $rules = [
            'section_type' => 'required|string',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ];

        // Aturan validasi kondisional berdasarkan section_type (sama seperti store)
        if ($request->input('section_type') === 'hero') {
            $rules = array_merge($rules, [
                'content.slides' => 'required|array',
                'content.slides.*.id' => 'nullable|string',
                'content.slides.*.title' => 'required|string|max:255',
                'content.slides.*.subtitle' => 'nullable|string|max:255',
                'content.slides.*.description' => 'nullable|string',
                'content.slides.*.button_text' => 'nullable|string|max:255',
                'content.slides.*.button_url' => 'nullable|string|max:255',
                'content.slides.*.background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'content.slides.*.current_background_image' => 'nullable|string',
            ]);
        } elseif ($request->input('section_type') === 'banner') {
            // Validasi untuk 3 banner
            $rules = array_merge($rules, [
                'content.title_1' => 'nullable|string|max:255',
                'content.url_1' => 'nullable|string|max:255',
                'content.button_text_1' => 'nullable|string|max:255',
                'content.image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'content.current_image_1' => 'nullable|string',

                'content.title_2' => 'nullable|string|max:255',
                'content.url_2' => 'nullable|string|max:255',
                'content.button_text_2' => 'nullable|string|max:255',
                'content.image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'content.current_image_2' => 'nullable|string',

                'content.title_3' => 'nullable|string|max:255',
                'content.url_3' => 'nullable|string|max:255',
                'content.button_text_3' => 'nullable|string|max:255',
                'content.image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'content.current_image_3' => 'nullable|string',
            ]);
        } elseif ($request->input('section_type') === 'text_block') {
            $rules = array_merge($rules, [
                'content.heading' => 'nullable|string|max:255',
                'content.body' => 'nullable|string',
            ]);
        } elseif ($request->input('section_type') === 'product_grid') {
            // Validasi untuk product_grid
            $rules = array_merge($rules, [
                'content.heading' => 'nullable|string|max:255',
                'content.filter_type' => 'required|in:latest,featured,bestsellers,custom',
                'content.product_ids' => 'nullable|string', // String ID dipisahkan koma
                'content.display_limit' => 'required|integer|min:1',
            ]);
        }
        // ... tambahkan kondisi untuk tipe seksi lainnya

        $request->validate($rules); // Jalankan validasi

        // Hapus file lama jika ada perubahan gambar pada konten
        $this->deleteOldContentFiles($section->content, $request->input('section_type'), $request);

        $content = $this->processSectionContent($request, $request->input('section_type'));

        $section->update([
            'section_type' => $request->input('section_type'),
            'order' => $request->input('order', 0),
            'is_active' => $request->boolean('is_active'),
            'content' => $content,
        ]);

        return redirect()->route('mitra.pages.sections.index', $page)->with('success', 'Section updated successfully!');
    }
   
// Menghapus seksi
    public function destroy(Page $page, PageSection $section)
    {
        // Hapus file terkait sebelum menghapus seksi
        $this->deleteOldContentFiles($section->content, $section->section_type, null, true);
        $section->delete();
        return redirect()->route('mitra.pages.sections.index', $page)->with('success', 'Section deleted successfully!');
    }

    // Helper untuk memproses dan menyimpan konten dinamis seksi
    private function processSectionContent(Request $request, string $sectionType): array
    {
        $content = [];
        switch ($sectionType) {
            case 'hero':
                $slidesData = [];
                $uploadedImagePaths = []; 
                if ($request->has('content.slides') && is_array($request->input('content.slides'))) {
                    foreach ($request->input('content.slides') as $index => $slideInput) {
                        $slide = [
                            'id' => $slideInput['id'] ?? (string) Str::uuid(), 
                            'subtitle' => $slideInput['subtitle'] ?? null,
                            'title' => $slideInput['title'] ?? null,
                            'description' => $slideInput['description'] ?? null,
                            'button_text' => $slideInput['button_text'] ?? null,
                            'button_url' => $slideInput['button_url'] ?? null,
                        ];

                        if ($request->hasFile("content.slides.{$index}.background_image")) {
                            $imageFile = $request->file("content.slides.{$index}.background_image");
                            $imagePath = $imageFile->store('section_images/hero', 'public');
                            $slide['background_image'] = $imagePath;
                            $uploadedImagePaths[] = $imagePath;
                        } else {
                            $slide['background_image'] = $slideInput['current_background_image'] ?? null;
                        }

                        $slidesData[] = $slide;
                    }
                }
                $content['slides'] = $slidesData;
                break;
            case 'banner': // MENGAMBIL DATA UNTUK 3 BANNER
                for ($i = 1; $i <= 3; $i++) {
                    $content['title_' . $i] = $request->input('content.title_' . $i);
                    $content['url_' . $i] = $request->input('content.url_' . $i);
                    $content['button_text_' . $i] = $request->input('content.button_text_' . $i);
                    if ($request->hasFile('content.image_' . $i)) {
                        $content['image_' . $i] = $request->file('content.image_' . $i)->store('section_images/banner', 'public');
                    } else {
                        $content['image_' . $i] = $request->input('content.current_image_' . $i);
                    }
                }
                break;
            case 'text_block':
                $content['heading'] = $request->input('content.heading');
                $content['body'] = $request->input('content.body');
                break;
            case 'product_grid':
                // Memproses data untuk product_grid
                $content['heading'] = $request->input('content.heading');
                $content['filter_type'] = $request->input('content.filter_type');
                $content['display_limit'] = $request->input('content.display_limit');
                // Pisahkan string ID produk menjadi array jika filter_type adalah 'custom'
                if ($content['filter_type'] === 'custom' && $request->input('content.product_ids')) {
                    $productIds = explode(',', $request->input('content.product_ids'));
                    $content['product_ids'] = array_map('trim', $productIds); // Hapus spasi ekstra
                } else {
                    $content['product_ids'] = []; // Kosongkan jika bukan 'custom'
                }
                break;
            default:
                break;
        }
        return $content;
    }

    // Helper untuk menghapus file lama saat update atau delete
    private function deleteOldContentFiles(?array $oldContent, string $sectionType, ?Request $request, bool $isDeletingSection = false)
    {
        if (!$oldContent) return;

        switch ($sectionType) {
            case 'hero':
                $currentImagePaths = [];
                if ($request && $request->has('content.slides') && is_array($request->input('content.slides'))) {
                    foreach ($request->input('content.slides') as $slideInput) {
                        if (isset($slideInput['background_image']) && !empty($slideInput['background_image'])) {
                            $currentImagePaths[] = $slideInput['background_image'];
                        }
                    }
                }
                if (isset($oldContent['slides']) && is_array($oldContent['slides'])) {
                    foreach ($oldContent['slides'] as $oldSlide) {
                        if (isset($oldSlide['background_image']) && Storage::disk('public')->exists($oldSlide['background_image'])) {
                            if ($isDeletingSection || !in_array($oldSlide['background_image'], $currentImagePaths)) {
                                Storage::disk('public')->delete($oldSlide['background_image']);
                            }
                        }
                    }
                }
                break;
            case 'banner': // MENGHAPUS GAMBAR UNTUK 3 BANNER
                $imageKeys = ['image_1', 'image_2', 'image_3'];
                foreach ($imageKeys as $key) {
                    if (isset($oldContent[$key]) && Storage::disk('public')->exists($oldContent[$key])) {
                        if ($isDeletingSection || ($request && $request->hasFile('content.' . $key))) {
                            Storage::disk('public')->delete($oldContent[$key]);
                        }
                    }
                }
                break;
            default:
                break;
        }
    }
}