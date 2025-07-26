<?php

namespace App\Services;

use App\Models\Testimoni;
use Illuminate\Http\Request;

class TestimonialService
{
    /**
     * Metode ini hanya akan mengambil testimoni yang statusnya 'published'
     * dan yang tidak terikat pada produk manapun (product_id is null).
     */
    public function getPublishedLandingPageTestimonials()
    {
        return Testimoni::where('status', 'published')
            ->whereNull('product_id')
            ->latest()
            ->get();
    }
    
    public function getPaginatedTestimonials(Request $request)
    {
        $search = $request->input('search');

        return Testimoni::with('user')
            ->whereNull('product_id')
            ->when($search, function ($query, $search) {
                // Cari berdasarkan nama atau isi testimoni
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    // Cari juga berdasarkan nama user yang terkait
                    ->orWhereHas('user', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%");
                });
            })
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->latest('created_at')
            ->paginate(5)
            ->appends($request->query());
    }

    public function getTestimonialById(string $id)
    {
        return Testimoni::findOrFail($id);
    }

    public function create(array $data)
    {
        // Jika testimoni dibuat manual, user_id akan null
        $data['user_id'] = null;
        $data['status'] = 'pending';

        return Testimoni::create($data);
    }

    /**
     * Untuk ulasan produk dari halaman pesanan.
     */
    public function createFromReview(array $data)
    {
        $data['status'] = 'published';
        return Testimoni::create($data);
    }

    public function update(string $id, array $data)
    {
        $testimonial = $this->getTestimonialById($id);
        $testimonial->update([
            'rating' => $data['rating'],
            'content' => $data['content'],
        ]);
        return $testimonial;
    }

    public function updateStatus(string $id, string $status)
    {
        $testimonial = $this->getTestimonialById($id);
        $testimonial->update(['status' => $status]);
        return $testimonial;
    }

    public function delete(string $id)
    {
        $testimonial = $this->getTestimonialById($id);
        $testimonial->delete();
    }
}
