@extends('layouts.mitra')
@section('title', 'Pilih Tema Toko Anda')

@section('content')
    <div class="max-w-4xl mx-auto mt-10">
        <h2 class="text-2xl font-bold mb-4">Pilih Template</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($templates as $template)
                <div class="border rounded-lg p-4 shadow">
                    <h3 class="text-lg font-semibold mb-2">{{ $template->name }}</h3>
                    <img src="{{ asset('storage/' . $template->preview_image) }}" alt="Preview" class="rounded mb-2">
                    @if ($currentTemplateId === $template->id)
                        <p class="text-green-600 font-bold">Template Saat Ini</p>
                    @else
                        <form method="POST" action="{{ route('mitra.editor.updateTheme') }}">
                            @csrf
                            <input type="hidden" name="template_id" value="{{ $template->id }}">
                            <button type="submit"
                                class="mt-2 w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded">
                                Gunakan Template Ini
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection
