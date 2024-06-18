<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div id="app">
        <div class="container mx-auto text-center my-[200px]">
            <div class="border-2 border-gray-300 rounded-xl p-20 mx-[200px]">
                <h1 class="my-5 text-3xl">Create a Short URL</h1>
                <url-shortener />
            </div>
        </div>

    </div>

</body>

</html>
