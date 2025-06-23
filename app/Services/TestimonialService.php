<?php

namespace App\Services;

use App\Models\Testimoni;
use Illuminate\Http\Request;

class TestimonialService
{
    public function getPaginatedTestimonials(Request $request)
    {
        $search = $request->input('search');

        return Testimoni::with('user')
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

    public function update(string $id, array $data)
    {
        $testimonial = $this->getTestimonialById($id);
        unset($data['status']);
        $testimonial->update($data);
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
