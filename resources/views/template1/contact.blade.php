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
                            <p>Kami selalu siap membantu. Temukan informasi kontak kami di bawah atau lihat lokasi kami di peta.</p>
                        </div>
                        
                        {{-- Menggunakan data dari variabel $contact jika ada --}}
                        @if(isset($contact) && $contact)
                            <ul>
                                <li>
                                    <h4>Alamat</h4>
                                    <p>{{ $contact->address_line1 ?? 'Alamat tidak tersedia' }} <br />
                                       {{ $contact->city ?? '' }}{{ $contact->state ? ', ' . $contact->state : '' }} {{ $contact->postal_code ?? '' }}
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
                        {{-- GANTI `src` DI BAWAH INI DENGAN KODE EMBED GOOGLE MAPS ANDA --}}
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.663116997452!2d101.43981507476259!3d0.5143365995133796!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31d5ac1c6363c89d%3A0x8f453c15a8f46cfd!2sXL%20Center%20Pekanbaru!5e0!3m2!1sen!2sid!4v1719766699309!5m2!1sen!2sid"
                            height="500"
                            style="border:0;"
                            allowfullscreen=""
                            aria-hidden="false"
                            tabindex="0">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endsection