<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\TestimonialService;
use Illuminate\Support\Facades\Auth;

class TestimoniController extends Controller
{
    protected $testimonialService;

    public function __construct(TestimonialService $testimonialService)
    {
        $this->testimonialService = $testimonialService;
    }

    public function index(Request $request)
    {
        $testimonials = $this->testimonialService->getPaginatedTestimonials($request);

        $search = $request->input('search');

        return view('dashboard-admin.kelola-testimoni', compact('testimonials', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5'
        ]);

        $this->testimonialService->create($validated);
        return redirect()->route('admin.testimoni.index')->with('success', 'Testimoni berhasil ditambahkan dan sedang menunggu persetujuan.');
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
            'rating' => 'required|integer|min:1|max:5'
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

    // == LANDING PAGE SUBMISSION METHOD == //

    /**
     * Menyimpan testimoni baru yang dikirim dari landing page.
     */
    public function submitFromLandingPage(Request $request)
    {
        $validated = $request->validate([
            'name' => Auth::check() ? 'nullable|string|max:255' : 'required|string|max:255',
            'content' => 'required|string|min:10',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $data = [
            'content' => $validated['content'],
            'rating' => $validated['rating'],
        ];

        if (Auth::check()) {
            $user = Auth::user();
            $data['user_id'] = $user->id;
            $data['name'] = $validated['name'] ?? $user->name;
        } else {
            $data['name'] = $validated['name'];
        }

        $this->testimonialService->create($data);

        return redirect()->route('beranda');
    }
}
