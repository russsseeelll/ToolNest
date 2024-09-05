<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CoSE IT Tools')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/scripts.js') }}"></script>
</head>
<body class="flex flex-col min-h-screen bg-gray-50 text-gray-900">

@include('partials.header')

<main class="flex-grow container mx-auto py-12 px-6">
    @yield('content')
</main>

@include('partials.footer')

@yield('scripts')

</body>
</html>
