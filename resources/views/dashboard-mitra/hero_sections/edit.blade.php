@extends('layouts.mitra')

@section('title', 'Edit Hero Section')

@section('content')
    <h1>Edit Hero Section</h1>
    <form action="{{ route('mitra.hero_sections.update', $heroSection) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="subtitle" class="form-label">Subtitle</label>
            <input type="text" class="form-control" id="subtitle" name="subtitle" value="{{ old('subtitle', $heroSection->subtitle) }}">
        </div>
        <div class="mb-3">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $heroSection->title) }}" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $heroSection->description) }}</textarea>
        </div>
        <div class="mb-3">
            <label for="button_text" class="form-label">Button Text</label>
            <input type="text" class="form-control" id="button_text" name="button_text" value="{{ old('button_text', $heroSection->button_text) }}">
        </div>
        <div class="mb-3">
            <label for="button_url" class="form-label">Button URL</label>
            <input type="text" class="form-control" id="button_url" name="button_url" value="{{ old('button_url', $heroSection->button_url) }}">
        </div>
        <div class="mb-3">
            <label for="background_image" class="form-label">Background Image</label>
            <input type="file" class="form-control" id="background_image" name="background_image">
            @if ($heroSection->background_image)
                <div class="mt-2">
                    Current Image: <img src="{{ asset('storage/' . $heroSection->background_image) }}" alt="Current Banner" width="150">
                </div>
            @endif
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('mitra.hero_sections.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection