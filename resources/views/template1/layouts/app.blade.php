<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Male_Fashion Template">
    <meta name="keywords" content="Male_Fashion, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Male-Fashion | @yield('title', 'Template')</title>

    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&display=swap"
    rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('template1/css/bootstrap.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('template1/css/font-awesome.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('template1/css/elegant-icons.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('template1/css/magnific-popup.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('template1/css/nice-select.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('template1/css/owl.carousel.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('template1/css/slicknav.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('template1/css/style.css') }}" type="text/css">
</head>

<body>
    <div id="preloder">
        <div class="loader"></div>
    </div>

    @include('template1.partials.offcanvas-menu')
    @include('template1.partials.header')
    @yield('content')

    @include('template1.partials.footer')
    @include('template1.partials.search')
    <script src="{{ asset('template1/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('template1/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('template1/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('template1/js/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('template1/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('template1/js/jquery.countdown.min.js') }}"></script>
    <script src="{{ asset('template1/js/jquery.slicknav.js') }}"></script>
    <script src="{{ asset('template1/js/mixitup.min.js') }}"></script>
    <script src="{{ asset('template1/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('template1/js/main.js') }}"></script>
</body>

</html>