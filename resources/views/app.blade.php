<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet"/>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/sass/main.sass', 'resources/js/app.js'])
    @endif
</head>
<body class="antialiased">
<main class="md:min-h-screen md:flex md:items-center md:justify-center py-16 lg:py-20">
    <div class="container">
        @auth
            <form action="{{ route('logOut') }}" method="post" class="w-12">
                @csrf
                @method('DELETE')

                <x-form.primary-button>Выйти</x-form.primary-button>
            </form>
        @endauth
        @if($message = flash()->get())
            <div class="{{ $message->getClass() }}">
                {{ $message->getMessage() }}
            </div>
        @endif
    </div>
</main>
</body>
</html>
