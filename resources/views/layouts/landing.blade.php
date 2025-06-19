<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts._partials.head')
</head>
<body class="bg-white-50 text-gray-800">

    @include('layouts._partials.navbar') 
    
    <main>
        @yield('content')
    </main>
    
    @include('layouts._partials.footer')
    
    @include('layouts._partials.scripts')
</body>
</html>