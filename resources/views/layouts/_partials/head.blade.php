
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>@yield('title', 'E-Commerce Garuda Cyber') - Percepat Pertumbuhan Bisnis Anda</title>
<meta name="keywords" content="ecommerce, garuda cyber, toko online, produk, jasa">
<meta name="description" content="E-Commerce Garuda Cyber: Temukan berbagai produk dan layanan terbaik.">
<meta name="author" content="Garuda Cyber">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="{{ asset('images/gci.png') }}" type="image/png">

<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Google Fonts: Inter -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Nunito&family=Open+Sans&family=Poppins:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;600;700&family=Sen:wght@400;700;800&family=Source+Sans+Pro:wght@400;600;700&display=swap"
    rel="stylesheet">
    

<style>
    body {
        font-family: 'Inter', sans-serif;
    }
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .no-scrollbar {
        -ms-overflow-style: none;
        /* IE and Edge */
        scrollbar-width: none;
        /* Firefox */
    }
</style>

@stack('styles')