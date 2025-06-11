@extends('layouts.landing')

@section('title', 'LandingPage')
@section('content')
    
    @include('landing-page.section.banner')
    @include('landing-page.section.layanan')
    @include('landing-page.section.highlight')
    @include('landing-page.section.tema')
    @include('landing-page.section.paket')
    @include('landing-page.section.slogan')
    @include('landing-page.section.faq')
    @include('landing-page.section.testimoni')
    
@endsection



{{-- @push('styles')

@endpush

@push('scripts')

@endpush --}}