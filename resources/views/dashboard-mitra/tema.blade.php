@extends('layouts.mitra')
@section('title', 'Pilih Tema Toko Anda')

@section('content')
    <div class="bg-gray-100 min-h-screen p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">

            {{-- Header Section --}}
            <div class="text-center mb-12">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800">Pilih Tampilan Profesional Untuk Toko Anda</h1>
                <p class="mt-3 text-lg text-gray-500 max-w-2xl mx-auto">Pilih salah satu tema di bawah ini untuk memulai.
                    Anda dapat mengubahnya kapan saja.</p>
            </div>

            {{-- Grid for Templates --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse ($templates as $template)
                    {{-- [MODIFIKASI] Tambahkan kondisi untuk hover effect --}}
                    <div
                        class="bg-white rounded-xl shadow-md overflow-hidden flex flex-col transition-all duration-300 {{ $template->status === 'active' ? 'hover:shadow-xl hover:-translate-y-1' : '' }}">

                        {{-- Image Preview with Overlay --}}
                        <div class="relative group">
                            {{-- [MODIFIKASI] Link preview tidak aktif jika status bukan 'active' --}}
                            <a href="{{ $template->status === 'active' ? route('template.preview', $template) : '#' }}"
                                target="_blank" class="block {{ $template->status !== 'active' ? 'pointer-events-none' : '' }}">

                                {{-- [MODIFIKASI] Tambahkan filter grayscale jika status bukan 'active' --}}
                                <img src="{{ asset('storage/' . $template->image_preview) }}"
                                    alt="Preview {{ $template->name }}"
                                    class="w-full h-56 object-cover object-top transition-transform duration-300 {{ $template->status === 'active' ? 'group-hover:scale-105' : 'filter grayscale' }}"
                                    onerror="this.onerror=null;this.src='https://placehold.co/600x400/f1f5f9/cbd5e1?text={{ urlencode($template->name) }}';">

                                {{-- [MODIFIKASI] Hanya tampilkan overlay preview jika template aktif --}}
                                @if($template->status === 'active')
                                    <div
                                        class="absolute inset-0 bg-black bg-opacity-20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <div
                                            class="text-white text-center bg-black bg-opacity-50 rounded-full py-2 px-4 flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Lihat Preview</span>
                                        </div>
                                    </div>
                                @endif
                            </a>
                        </div>

                        {{-- Card Body --}}
                        <div class="p-6 flex flex-col flex-grow">
                            <h3 class="text-xl font-bold text-gray-800">{{ $template->name }}</h3>
                            <p class="text-gray-500 mt-2 flex-grow">{{ $template->description }}</p>

                            {{-- Action Button/Status Badge --}}
                            <div class="mt-6">
                                {{-- [MODIFIKASI] Tambahkan kondisi untuk status 'coming_soon' atau lainnya --}}
                                @if ($template->status !== 'active')
                                    <div
                                        class="w-full text-center bg-gray-200 text-gray-600 font-bold py-2.5 px-4 rounded-lg flex items-center justify-center gap-2 cursor-not-allowed">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Segera Hadir</span>
                                    </div>
                                @elseif ($currentTemplateId === $template->id)
                                    <div
                                        class="w-full text-center bg-green-100 text-green-800 font-bold py-2.5 px-4 rounded-lg flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>Tema Aktif</span>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('mitra.editor.updateTheme') }}">
                                        @csrf
                                        <input type="hidden" name="template_id" value="{{ $template->id }}">
                                        <button type="submit"
                                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-lg transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            Gunakan Template Ini
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-3 text-center py-16 text-gray-500 border-2 border-dashed rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900">Belum Ada Template</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Saat ini belum ada template yang tersedia. Silakan cek kembali nanti.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection