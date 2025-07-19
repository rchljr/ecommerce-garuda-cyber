@extends('template1.layouts.template')

@section('title', 'Kontak Kami')

@push('styles')
    {{-- Font Awesome untuk ikon --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .contact {
            padding-bottom: 50px;
        }

        .contact__text .section-title {
            text-align: left;
            margin-bottom: 40px;
        }

        .contact__text .section-title span {
            font-size: 14px;
            color: #111111;
            /* PERBAIKAN: Warna diselaraskan dengan tema */
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .contact__text .section-title h2 {
            color: #111111;
            font-weight: 700;
            line-height: 46px;
            margin-top: 10px;
        }

        .contact__text .section-title p {
            margin-bottom: 0;
            color: #555;
        }

        .contact__details {
            margin-top: 30px;
        }

        .contact__details .contact__item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 30px;
        }

        .contact__details .contact__item .icon {
            font-size: 24px;
            color: #111111;
            /* PERBAIKAN: Warna diselaraskan dengan tema */
            margin-right: 20px;
            width: 40px;
            text-align: center;
        }

        .contact__details .contact__item .text h4 {
            color: #111;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .contact__details .contact__item .text p {
            margin-bottom: 0;
            color: #555;
            line-height: 1.7;
        }

        .contact__form {
            padding: 40px;
            background: #f8f8f8;
            border-radius: 8px;
        }

        .contact__form h2 {
            color: #111111;
            font-weight: 700;
            margin-bottom: 30px;
        }

        .contact__form form input,
        .contact__form form textarea {
            width: 100%;
            height: 50px;
            font-size: 14px;
            color: #555;
            padding-left: 20px;
            margin-bottom: 20px;
            border: 1px solid #e1e1e1;
            border-radius: 25px;
        }

        .contact__form form textarea {
            height: 150px;
            padding-top: 15px;
            border-radius: 15px;
        }

        .contact__form form button {
            width: 100%;
            border-radius: 25px;
        }

        .map-container {
            margin-top: 40px;
            border-radius: 8px;
            overflow: hidden;
        }
    </style>
@endpush

@section('content')
    @php
        $currentSubdomain = request()->route('subdomain');
    @endphp

    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-blog set-bg" data-setbg="{{ asset('template1/img/breadcrumb-bg.jpg') }}">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Kontak Kami</h2>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Contact Section Begin -->
    <section class="contact spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="contact__text">
                        <div class="section-title">
                            <span>Informasi</span>
                            <h2>Hubungi Kami</h2>
                            <p>Kami selalu siap membantu. Hubungi kami melalui detail di bawah ini atau kirimkan pesan
                                langsung melalui formulir.</p>
                        </div>
                        <div class="contact__details">
                            @if (isset($contact) && $contact)
                                <div class="contact__item">
                                    <div class="icon"><i class="fa fa-map-marker"></i></div>
                                    <div class="text">
                                        <h4>Alamat</h4>
                                        <p>{{ $contact->address_line1 ?? 'Alamat belum diatur.' }}<br />
                                            {{ $contact->city ?? '' }}{{ $contact->state ? ', ' . $contact->state : '' }}
                                            {{ $contact->postal_code ?? '' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="contact__item">
                                    <div class="icon"><i class="fa fa-phone"></i></div>
                                    <div class="text">
                                        <h4>Telepon</h4>
                                        <p>{{ $contact->phone ?? 'Telepon belum diatur.' }}</p>
                                    </div>
                                </div>
                                <div class="contact__item">
                                    <div class="icon"><i class="fa fa-envelope"></i></div>
                                    <div class="text">
                                        <h4>Email</h4>
                                        <p>{{ $contact->email ?? 'Email belum diatur.' }}</p>
                                    </div>
                                </div>
                                <div class="contact__item">
                                    <div class="icon"><i class="fa fa-clock"></i></div>
                                    <div class="text">
                                        <h4>Jam Kerja</h4>
                                        <p>{!! nl2br(e($contact->working_hours ?? 'Jam kerja belum diatur.')) !!}</p>
                                    </div>
                                </div>
                            @else
                                <p>Informasi kontak belum diatur oleh pemilik toko.</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="map-container">
                        @if (isset($contact->map_embed_code) && $contact->map_embed_code)
                            {!! $contact->map_embed_code !!}
                        @else
                            <div
                                style="width: 100%; height: 450px; background-color: #f3f3f3; display: flex; align-items: center; justify-content: center; border-radius: 5px;">
                                <p style="color: #666;">Lokasi peta tidak tersedia.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Contact Section End -->
@endsection