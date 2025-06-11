<?php

namespace App\Services;

use App\Models\LandingPage;

class LandingPageService
{
    public function getAll()
    {
        return LandingPage::all();
    }

    public function getById($id)
    {
        return LandingPage::findOrFail($id);
    }

    public function create(array $data)
    {
        return LandingPage::create($data);
    }

    public function update($id, array $data)
    {
        $landingPage = LandingPage::findOrFail($id);
        $landingPage->update($data);
        return $landingPage;
    }

    public function delete($id)
    {
        $landingPage = LandingPage::findOrFail($id);
        $landingPage->delete();
        return true;
    }
}