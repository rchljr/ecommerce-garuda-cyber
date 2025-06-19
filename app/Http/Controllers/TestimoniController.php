<?php
// File: app/Http/Controllers/TestimonialController.php

namespace App\Http\Controllers;

use App\Services\TestimonialService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TestimoniController extends Controller
{
    protected $testimonialService;

    public function __construct(TestimonialService $testimonialService)
    {
        $this->testimonialService = $testimonialService;
    }

    public function index()
    {
        $testimonials = $this->testimonialService->getAllTestimonials();
        return view('dashboard-admin.kelola-testimoni', compact('testimonials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'status' => ['required', Rule::in(['published', 'pending'])],
        ]);

        $this->testimonialService->create($validated);
        return redirect()->route('admin.testimoni.index')->with('success', 'Testimoni berhasil ditambahkan.');
    }

    public function showJson($id)
    {
        return response()->json($this->testimonialService->getTestimonialById($id));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'status' => ['required', Rule::in(['published', 'pending'])],
        ]);

        $this->testimonialService->update($id, $validated);
        return redirect()->route('admin.testimoni.index')->with('success', 'Testimoni berhasil diperbarui.');
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['published', 'pending'])],
        ]);

        $this->testimonialService->updateStatus($id, $validated['status']);
        $message = $validated['status'] === 'published' ? 'Testimoni berhasil ditampilkan.' : 'Testimoni berhasil disembunyikan.';
        return back()->with('success', $message);
    }

    public function destroy($id)
    {
        $this->testimonialService->delete($id);
        return redirect()->route('admin.testimoni.index')->with('success', 'Testimoni berhasil dihapus.');
    }
}
