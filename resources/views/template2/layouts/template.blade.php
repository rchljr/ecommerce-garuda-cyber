@include('template2.includes.header')

<main>
    @yield('content')
</main>

@include('template2.includes.footer')

@include('template2.includes.scripts')

@stack('scripts')
@stack('page-scripts')

</body>
</html>