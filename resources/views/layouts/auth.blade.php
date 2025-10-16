<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'Travora' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-stone-50">
    <div class="min-h-screen flex flex-col items-center justify-center p-4">
        <div class="mb-6 text-center">
            <h1 class="text-4xl font-bold text-emerald-600">Travora</h1>
            <p class="text-gray-500">Find Your Green Path</p>
        </div>

        <div class="w-full max-w-lg bg-white rounded-xl shadow-lg p-8">
            @yield('content')
        </div>
    </div>
</body>
</html>