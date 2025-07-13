@extends('template1.layouts.template')

@section('title', 'Kontak Kami')

@section('content')

    <section class="breadcrumb-blog set-bg" data-setbg="{{ asset('template1/img/breadcrumb-bg.jpg') }}">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Kontak Kami</h2>
                </div>
            </div>
        </div>
    </section>
    <section class="contact spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="contact__text">
                        <div class="section-title">
                            <span>Informasi</span>
                            <h2>Detail Kontak</h2>
                            <p>Kami selalu siap membantu. Temukan informasi kontak kami di bawah atau lihat lokasi kami di
                                peta.</p>
                        </div>

                        {{-- Menggunakan data dari variabel $contact jika ada --}}
                        @if (isset($contact) && $contact)
                            <ul>
                                <li>
                                    <h4>Alamat</h4>
                                    <p>{{ $contact->address_line1 ?? 'Alamat tidak tersedia' }} <br />
                                        {{ $contact->city ?? '' }}{{ $contact->state ? ', ' . $contact->state : '' }}
                                        {{ $contact->postal_code ?? '' }}
                                    </p>
                                </li>
                                <li>
                                    <h4>Telepon</h4>
                                    <p>{{ $contact->phone ?? 'Nomor tidak tersedia' }}</p>
                                </li>
                                <li>
                                    <h4>Email</h4>
                                    <p>{{ $contact->email ?? 'Email tidak tersedia' }}</p>
                                </li>
                                <li>
                                    <h4>Jam Kerja</h4>
                                    <p>{{ $contact->working_hours ?? 'Jam kerja tidak tersedia' }}</p>
                                </li>
                            </ul>
                            {{-- Tampilan default jika tidak ada data --}}
                        @else
                            <p>Informasi kontak belum diatur oleh admin.</p>
                        @endif
                    </div>
                </div>

                <div class="col-lg-6 col-md-6">
                    <div class="contact__map">
                        {{-- Kode yang sudah diperbaiki --}}
                        @if (isset($mapEmbedCode) && !empty($mapEmbedCode))
                            <div class="mt-5">
                                <h3 class="fw-bold mb-3">Lokasi Kami</h3>
                                <a href="{{ $googleMapsLink }}" target="_blank" rel="noopener noreferrer"
                                    class="d-block position-relative map-container-link">

                                    {{-- Lapisan transparan di atas peta untuk menangkap klik --}}
                                    <div
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 2;">
                                    </div>

                                    {{-- Kode Embed Peta Anda --}}
                                    <div class="ratio ratio-16x9">
                                        {!! $mapEmbedCode !!}
                                    </div>

                                    {{-- Tambahkan styling pada iframe jika perlu agar tidak menangkap klik mouse --}}
                                    <style>
                                        .map-container-link iframe {
                                            pointer-events: none;
                                            border-radius: 0.5rem;
                                            /* Membuat sudut lebih manis */
                                        }
                                    </style>
                                </a>
                                <div class="text-center mt-2">
                                    <a href="{{ $googleMapsLink }}" target="_blank" rel="noopener noreferrer"
                                        class="btn btn-outline-primary">
                                        <i class="bi bi-geo-alt-fill"></i> Buka di Google Maps
                                    </a>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
