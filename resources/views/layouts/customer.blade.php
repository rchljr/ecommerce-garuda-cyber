<!DOCTYPE html>
<html lang="id">

<head>
    @include('layouts._partials.head')
</head>

<body class="cus-auth-body">
    <div class="cus-auth-wrapper">
        @include('layouts._partials.header-cust')
        <main class="cus-auth-main">
            @yield('content')
        </main>
        @include('layouts._partials.footer-customer')
    </div>
</body>


</html>

@push('styles')
    <style>
        body {
            background: #f6f7fb;
            margin: 0;
            font-family: 'Inter', Arial, sans-serif;
            color: #222;
        }

@endpush