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
                        @if ($mapEmbedCode)
                            {{-- Gunakan {!! !!} untuk merender HTML mentah dari database --}}
                            {!! $mapEmbedCode !!}
                        @else
                            {{-- Tampilkan pesan jika kode embed tidak ditemukan di database --}}
                            <div class="w-full h-[500px] bg-gray-200 flex items-center justify-center">
                                <p class="text-gray-500">Lokasi peta tidak tersedia.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
