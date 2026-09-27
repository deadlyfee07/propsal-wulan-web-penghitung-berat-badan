<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', 'BMI & Calorie Analyzer')</title>
        <meta name="description" content="Aplikasi pengukur berat badan berbasis BMI dan analisis caloric untuk media pembelajaran dan edukasi.">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-100 font-sans text-slate-800 antialiased">
        <div class="mx-auto flex min-h-screen w-full max-w-3xl flex-col px-4 py-8 sm:px-6 sm:py-12">
            <header class="mb-8 text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">
                    Projek KKA &amp; PIPAS
                </p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                    BMI &amp; Calorie Analyzer
                </h1>
                <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-slate-600 sm:text-base">
                    Aplikasi Pengukur Berat Badan Berbasis BMI dan Analisis Kalori.
                    Masukkan data Anda untuk memperoleh hasil perhitungan edukatif.
                </p>
            </header>

            <main class="flex-1">
                @yield('content')
            </main>

            <footer class="mt-10 border-t border-slate-200 pt-5 text-center">
                <p class="text-xs leading-relaxed text-slate-500">
                    Hasil perhitungan pada aplikasi ini merupakan
                    <strong>estimasi untuk tujuan edukasi</strong> dan
                    <strong>tidak digunakan sebagai diagnosis medis</strong>.
                    Untuk pemeriksaan kondisi kesehatan, silakan konsultasikan ke tenaga kesehatan.
                </p>
            </footer>
        </div>
    </body>
</html>
