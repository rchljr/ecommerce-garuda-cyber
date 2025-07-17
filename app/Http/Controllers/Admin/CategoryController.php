<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\CategoryService;
use App\Http\Controllers\BaseController;

class CategoryController extends BaseController
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->getAllCategories();
        return view('dashboard-admin.kelola-kategori', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'subcategories' => 'nullable|array',
            'subcategories.*' => 'nullable|string|max:255',
        ]);

        $this->categoryService->create($request->all());
        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function showJson($id)
    {
        return response()->json($this->categoryService->getCategoryById($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($id)],
            'subcategories' => 'nullable|array',
            'subcategories.*' => 'nullable|string|max:255',
        ]);

        $this->categoryService->update($id, $request->all());
        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->categoryService->delete($id);
        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
