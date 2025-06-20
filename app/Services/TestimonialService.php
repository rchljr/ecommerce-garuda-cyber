<?php

namespace App\Services;

use App\Models\Testimoni;

class TestimonialService
{
    public function getPaginatedTestimonials()
    {
        return Testimoni::with('user')
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->latest('created_at')
            ->paginate(5);
    }
    public function getAllTestimonials()
    {
        return Testimoni::with('user')->latest()->get();
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
