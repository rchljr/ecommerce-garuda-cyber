<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts._partials.head')
</head>
<body>
    @include('layouts._partials.navbar') 
    
    <main>
        @yield('content')
    </main>
    
    @include('layouts._partials.footer')
    
    @include('layouts._partials.scripts')
</body>
</html>