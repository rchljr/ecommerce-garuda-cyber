@extends('layouts.customer')

@section('title', 'Login')
@section('page_title', 'Login')

@section('content')
<div class="cus-auth-hero">
    <div class="cus-auth-hero-left">
        <div class="cus-auth-hero-logo"></div>
        <div class="cus-auth-hero-brand">Belanja di <span>YourBrand</span></div>
        <div class="cus-auth-hero-desc">Mudah, Aman, dan Cepat</div>
    </div>
    <div class="cus-auth-card">
        <h2 class="cus-auth-card-title">Login Customer</h2>
        <form>
            <input type="email" placeholder="Email" class="cus-auth-input" required>
            <input type="password" placeholder="Password" class="cus-auth-input" required>
            <button type="submit" class="cus-auth-btn">Masuk</button>
        </form>
        <div class="cus-auth-divider">atau</div>
        <div class="cus-auth-social">
            <button class="cus-auth-social-btn">Google</button>
            <button class="cus-auth-social-btn">Facebook</button>
        </div>
        <div class="cus-auth-switch">
            Belum punya akun? <a href="{{ route('register-cust') }}">Daftar</a>
        </div>
    </div>
</div>
@endsection
