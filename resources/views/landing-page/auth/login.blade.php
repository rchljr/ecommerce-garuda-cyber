<!DOCTYPE html>
<html lang="en">

<head>
    @section('title', 'Login')
    @include('layouts._partials.head')
</head>

<body>
    <div class="login-wrapper">
        @include('layouts._partials.auth')
        <div class="login-right">
            <form class="login-form" method="POST" action="{{ route('login.submit') }}">
                @csrf
                <h2 class="form-title">Login</h2>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Masukkan Email Anda" required
                        value="{{ old('email') }}">
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan Kata Sandi" required>
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <button type="submit" class="login-btn">Login</button>
            </form>
        </div>
    </div>

    @include('layouts._partials.scripts')
</body>

@push('styles')
    <style>
        .body {
            background: #f6f6f6;
            font-family: 'Nunito', 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
    </style>
@endpush

{{-- @push('scripts')
@endpush --}}

</html>