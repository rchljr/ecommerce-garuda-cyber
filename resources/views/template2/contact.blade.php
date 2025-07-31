@extends('template2.layouts.template')

@section('title', 'Hubungi Kami')

@section('content')
    @php
        // Persiapan variabel untuk tenant dan mode preview, memastikan konsistensi
        $isPreview = $isPreview ?? false;
        $currentSubdomain = !$isPreview ? request()->route('subdomain') : null;
    @endphp

    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h4>Hubungi Kami</h4>
                        <div class="breadcrumb__links">
                            <a
                                href="{{ !$isPreview ? route('tenant.home', ['subdomain' => $currentSubdomain]) : '#' }}">Beranda</a>
                            <span>Kontak</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section contact-section bg-light">
        <div class="container">
            {{-- Menampilkan Info Kontak Dinamis dari Controller --}}
            <div class="row d-flex mb-5 contact-info">
                <div class="w-100"></div>
                <div class="col-md-3 d-flex">
                    <div class="info bg-white p-4 w-100">
                        <p><span>Alamat:</span> {{ optional($contact)->address_line1 ?? 'Alamat belum diatur' }}</p>
                        <p><span>Kota:</span> {{ optional($contact)->city ?? 'Kota belum diatur' }}</p>
                        <p><span>Provinsi:</span> {{ optional($contact)->city ?? 'Provinsi belum diatur' }}</p>
                    </div>
                </div>
                <div class="col-md-3 d-flex">
                    <div class="info bg-white p-4 w-100">
                        <p><span>Telepon:</span> <a
                                href="tel://{{ optional($contact)->phone ?? '' }}">{{ optional($contact)->phone ?? 'N/A' }}</a>
                        </p>
                    </div>
                </div>
                <div class="col-md-3 d-flex">
                    <div class="info bg-white p-4 w-100">
                        <p><span>Email:</span> <a
                                href="mailto:{{ optional($contact)->email ?? '' }}">{{ optional($contact)->email ?? 'N/A' }}</a>
                        </p>
                    </div>
                </div>
                <div class="col-md-3 d-flex">
                    <div class="info bg-white p-4 w-100">
                        <p><span>Website</span> <a href="{{ optional($contact)->website ?? '#' }}"
                                target="_blank">{{ optional($contact)->website ? basename(optional($contact)->website) : 'Belum diatur' }}</a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="row block-9">
                <div class="col-md-6 order-md-last d-flex">
                    {{-- Form Kontak yang Fungsional --}}
                    <form action="{{ !$isPreview ? route('tenant.contact', ['subdomain' => $currentSubdomain]) : '#' }}"
                        method="POST" class="bg-white p-5 contact-form">
                        @csrf

                        {{-- Menampilkan notifikasi sukses atau error --}}
                        @if(session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger">
                                Harap periksa kembali isian Anda.
                            </div>
                        @endif

                        <div class="form-group">
                            <input type="text" name="name" class="form-control" placeholder="Nama Anda"
                                value="{{ old('name') }}" required>
                            @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" class="form-control" placeholder="Email Anda"
                                value="{{ old('email') }}" required>
                            @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <input type="text" name="subject" class="form-control" placeholder="Subjek Pesan"
                                value="{{ old('subject') }}" required>
                            @error('subject') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <textarea name="message" cols="30" rows="7" class="form-control" placeholder="Isi Pesan"
                                required>{{ old('message') }}</textarea>
                            @error('message') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary py-3 px-5" {{ $isPreview ? 'disabled' : '' }}>Kirim
                                Pesan</button>
                        </div>
                    </form>
                </div>

                <div class="col-md-6 d-flex">
                    {{-- Menampilkan Peta dari Embed Code yang ada di Controller --}}
                    <div class="bg-white" style="width: 100%; height: 100%;">
                        {!! optional($contact)->map_embed_code ?? '<div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light"><p>Peta tidak tersedia.</p></div>' !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection