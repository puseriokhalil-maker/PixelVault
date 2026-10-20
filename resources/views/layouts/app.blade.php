<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PixelVault')</title>
    
    <!-- This compiles your Tailwind CSS and JS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-white font-sans text-slate-900 antialiased">

    <!-- Navbar Component -->
    @include('components.navbar')

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer Component -->
    @include('components.footer')

</body>
</html>