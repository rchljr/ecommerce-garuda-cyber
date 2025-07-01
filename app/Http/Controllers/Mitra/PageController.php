<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::all();
        return view('dashboard-mitra.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('dashboard-mitra.pages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:pages,name',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        Page::create($data);

        return redirect()->route('dashboard-mitra.pages.index')->with('success', 'Page created successfully!');
    }

    public function edit(Page $page)
    {
        return view('dashboard-mitra.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:pages,name,'.$page->id,
            'slug' => 'nullable|string|max:255|unique:pages,slug,'.$page->id,
            'title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $page->update($data);

        return redirect()->route('dashboard-mitra.pages.index')->with('success', 'Page updated successfully!');
    }

    public function destroy(Page $page)
    {
        $page->delete(); // Ini akan menghapus PageSections terkait karena onDelete('cascade')
        return redirect()->route('dashboard-mitra.pages.index')->with('success', 'Page deleted successfully!');
    }
}