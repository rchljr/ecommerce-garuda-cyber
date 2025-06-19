<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryService
{
    public function getAllCategories()
    {
        return Category::with('subcategories')->latest()->get();
    }

    public function getCategoryById(string $id)
    {
        return Category::with('subcategories')->findOrFail($id);
    }

    public function create(array $data)
    {
        DB::transaction(function () use ($data) {
            $category = Category::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
            ]);

            if (!empty($data['subcategories'])) {
                foreach ($data['subcategories'] as $subCategoryName) {
                    if (!empty($subCategoryName)) {
                        $category->subcategories()->create([
                            'name' => $subCategoryName,
                            'slug' => Str::slug($subCategoryName),
                        ]);
                    }
                }
            }
        });
    }

    public function update(string $id, array $data)
    {
        DB::transaction(function () use ($id, $data) {
            $category = $this->getCategoryById($id);
            $category->update([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
            ]);

            // Hapus subkategori lama dan buat yang baru
            $category->subcategories()->delete();

            if (!empty($data['subcategories'])) {
                foreach ($data['subcategories'] as $subCategoryName) {
                    if (!empty($subCategoryName)) {
                        $category->subcategories()->create([
                            'name' => $subCategoryName,
                            'slug' => Str::slug($subCategoryName),
                        ]);
                    }
                }
            }
        });
    }

    public function delete(string $id)
    {
        $category = $this->getCategoryById($id);
        $category->delete();
    }
}