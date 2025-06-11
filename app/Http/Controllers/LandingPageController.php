<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Services\LandingPageService;
use App\Http\Resources\LandingPageResource;
use Illuminate\Http\Request;

class LandingPageController extends BaseController
{
    protected $service;

    public function __construct(LandingPageService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $data = $this->service->getAll();
        return $this->sendResponse(LandingPageResource::collection($data), 'Landing pages retrieved successfully.');
    }

    public function show($id)
    {
        try {
            $data = $this->service->getById($id);
            return $this->sendResponse(new LandingPageResource($data), 'Landing page retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Landing page not found.', 404);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'total_users' => 'required|integer|min:0',
            'total_shops' => 'required|integer|min:0',
            'total_visitors' => 'required|integer|min:0',
            'total_transactions' => 'required|integer|min:0',
        ]);
        $data = $this->service->create($validated);
        return $this->sendResponse(new LandingPageResource($data), 'Landing page created successfully.', 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'total_users' => 'required|integer|min:0',
            'total_shops' => 'required|integer|min:0',
            'total_visitors' => 'required|integer|min:0',
            'total_transactions' => 'required|integer|min:0',
        ]);
        try {
            $data = $this->service->update($id, $validated);
            return $this->sendResponse(new LandingPageResource($data), 'Landing page updated successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Landing page not found.', 404);
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete($id);
            return $this->sendResponse(null, 'Landing page deleted successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Landing page not found.', 404);
        }
    }
}