@extends('layouts.app')

@php
    use App\Enums\KategoriBmi;

    $warna = match ($hasil->kategori) {
        KategoriBmi::Kurang => 'border-sky-200 bg-sky-50 text-sky-700',
        KategoriBmi::Normal => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        KategoriBmi::Lebih => 'border-amber-200 bg-amber-50 text-amber-700',
        KategoriBmi::Obesitas => 'border-rose-200 bg-rose-50 text-rose-700',
    };

    $bmiAngka = fn (float $nilai, int $desimal = 2) => number_format($nilai, $desimal, ',', '.');
    $posisi = max(0.0, min(100.0, (($hasil->bmi - 15) / 25) * 100));
    $posisiCss = number_format($posisi, 2, '.', '');
    $faktor = $hasil->aktivitasFisik->faktorTerformat();
@endphp

@section('title', 'Hasil Perhitungan — BMI & Calorie Analyzer')

@section('content')
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <h2 class="text-lg font-semibold text-slate-900">Hasil Perhitungan</h2>

        <div class="mt-6 grid gap-5 sm:grid-cols-2">
            <div class="rounded-xl bg-slate-900 p-5 text-white">
                <p class="text-xs font-medium tracking-wide text-slate-400 uppercase">BMI Anda</p>
                <p class="mt-1 text-3xl font-bold tracking-tight">
                    {{ $bmiAngka($hasil->bmi) }}
                    <span class="text-sm font-medium text-slate-400">kg/m²</span>
                </p>
                <p class="mt-3 text-xs font-medium tracking-wide text-slate-400 uppercase">Kategori</p>
                <span class="mt-1 inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $warna }}">
                    {{ $hasil->kategori->label() }}
                </span>
            </div>

            <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-5">
                <p class="text-xs font-medium tracking-wide text-indigo-500 uppercase">Estimasi Kebutuhan Energi</p>
                <p class="mt-1 text-3xl font-bold tracking-tight text-indigo-950">
                    {{ number_format($hasil->kebutuhanEnergi, 0, ',', '.') }}
                    <span class="text-sm font-medium text-indigo-400">kkal/hari</span>
                </p>
                <p class="mt-3 text-xs leading-relaxed text-indigo-700">
                    Berdasarkan basal metabolisme
                    <strong>{{ $bmiAngka($hasil->basalMetabolisme, 0) }} kkal/hari</strong>
                    dikalikan faktor aktivitas
                    <strong>{{ $faktor }}</strong>
                    ({{ $hasil->aktivitasFisik->label() }}).
                </p>
            </div>
        </div>

        <div class="mt-5 rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between text-xs font-medium text-slate-500">
                <span>15</span>
                <span>25</span>
                <span>35</span>
            </div>
            <div class="relative mt-2 h-2.5 w-full overflow-hidden rounded-full bg-slate-100">
                <div class="flex h-full w-full">
                    <div class="w-[14%] bg-sky-300"></div>
                    <div class="w-[26%] bg-emerald-400"></div>
                    <div class="w-[8%] bg-amber-400"></div>
                    <div class="w-[52%] bg-rose-400"></div>
                </div>
                <div class="absolute top-1/2 -mt-[7px] -ml-[7px] h-3.5 w-3.5 -translate-y-0 rounded-full border-2 border-white bg-slate-900 shadow"
                    style="left: {{ $posisiCss }}%" aria-hidden="true"></div>
            </div>
            <p class="mt-3 text-sm text-slate-600">{{ $hasil->kategori->deskripsi() }}</p>
        </div>

        <dl class="mt-5 grid gap-3 sm:grid-cols-2">
            <div class="rounded-xl border border-slate-200 px-4 py-3">
                <dt class="text-xs text-slate-500">Rentang Berat Ideal</dt>
                <dd class="mt-0.5 text-sm font-semibold text-slate-800">
                    {{ $bmiAngka($hasil->beratIdealMinimum, 1) }} – {{ $bmiAngka($hasil->beratIdealMaksimum, 1) }} kg
                </dd>
                <dd class="mt-0.5 text-xs text-slate-400">
                    Dihitung dari BMI {{ $bmiAngka(\App\Services\BmiCalculator::BMI_NORMAL_MIN, 1) }}–{{ $bmiAngka(\App\Services\BmiCalculator::BMI_NORMAL_MAX, 1) }} pada tinggi {{ $bmiAngka($hasil->tinggiMeter) }} m.
                </dd>
            </div>
            <div class="rounded-xl border border-slate-200 px-4 py-3">
                <dt class="text-xs text-slate-500">Data yang Digunakan</dt>
                <dd class="mt-0.5 text-sm font-semibold text-slate-800">
                    {{ $hasil->usia }} tahun · {{ $hasil->jenisKelamin->label() }}
                </dd>
                <dd class="mt-0.5 text-xs text-slate-400">
                    {{ $bmiAngka($hasil->beratBadan, 1) }} kg · {{ $bmiAngka($hasil->tinggiBadan, 1) }} cm ·
                    {{ $hasil->aktivitasFisik->label() }}
                </dd>
            </div>
        </dl>
    </div>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white/70 p-6 sm:p-7">
        <h3 class="text-sm font-semibold text-slate-900">Rincian Perhitungan</h3>

        <ol class="mt-4 space-y-4 text-sm">
            <li class="flex gap-3">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-slate-200 text-xs font-semibold text-slate-700">1</span>
                <p class="text-slate-600">
                    Konversi tinggi badan:
                    <span class="font-medium text-slate-800">{{ $bmiAngka($hasil->tinggiBadan, 0) }} cm ÷ 100 =
                        {{ $bmiAngka($hasil->tinggiMeter) }} m</span>
                </p>
            </li>
            <li class="flex gap-3">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-slate-200 text-xs font-semibold text-slate-700">2</span>
                <p class="text-slate-600">
                    BMI = berat badan ÷ tinggi badan²
                    <span class="block font-medium text-slate-800">
                        {{ $bmiAngka($hasil->beratBadan, 1) }} ÷ ({{ $bmiAngka($hasil->tinggiMeter) }} ×
                        {{ $bmiAngka($hasil->tinggiMeter) }}) =
                        {{ $bmiAngka($hasil->bmi) }} kg/m²
                    </span>
                </p>
            </li>
            <li class="flex gap-3">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-slate-200 text-xs font-semibold text-slate-700">3</span>
                <p class="text-slate-600">
                    Penentuan kategori berdasarkan BMI
                    <span class="font-medium text-slate-800">{{ $hasil->kategori->rentang() }} kg/m² →
                        {{ $hasil->kategori->label() }}</span>
                </p>
            </li>
            <li class="flex gap-3">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-slate-200 text-xs font-semibold text-slate-700">4</span>
                <p class="text-slate-600">
                    Basal metabolisme (Mifflin-St Jeor)
                    <span class="block font-medium text-slate-800">
                        (10 × {{ $bmiAngka($hasil->beratBadan, 1) }}) + (6,25 × {{ $bmiAngka($hasil->tinggiBadan, 0) }})
                        − (5 × {{ $hasil->usia }})
                        {{ $hasil->jenisKelamin->konstantaMifflin() >= 0 ? '+' : '−' }}
                        {{ abs($hasil->jenisKelamin->konstantaMifflin()) }}
                        = {{ $bmiAngka($hasil->basalMetabolisme, 0) }} kkal
                    </span>
                </p>
            </li>
            <li class="flex gap-3">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-slate-200 text-xs font-semibold text-slate-700">5</span>
                <p class="text-slate-600">
                    Kebutuhan energi = basal metabolisme × faktor aktivitas
                    <span class="block font-medium text-slate-800">
                        {{ $bmiAngka($hasil->basalMetabolisme, 0) }} ×
                        {{ $faktor }} =
                        {{ number_format($hasil->kebutuhanEnergi, 0, ',', '.') }} kkal/hari
                    </span>
                </p>
            </li>
        </ol>
    </section>

    <section class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-6 sm:p-7">
        <h3 class="text-sm font-semibold text-amber-900">Informasi Edukasi</h3>
        <ul class="mt-3 space-y-2 text-sm leading-relaxed text-amber-900/80">
            <li>BMI adalah indikator sederhana yang menghubungkan berat badan dan tinggi badan, bukan alat ukur langsung komposisi tubuh.</li>
            <li>{{ $hasil->aktivitasFisik->deskripsi() }} Tingkat aktivitas yang dipilih memengaruhi estimasi kebutuhan energi.</li>
            <li class="font-semibold">Angka-angka ini adalah estimasi edukatif dan tidak berlaku sebagai diagnosis medis.</li>
        </ul>
    </section>

    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('bmi.form') }}"
            class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-3 text-sm font-semibold tracking-wide text-white uppercase shadow-sm transition hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500/40 focus:outline-none focus:ring-offset-2">
            Hitung Ulang
        </a>
        <p class="text-xs text-slate-500 sm:text-right">
            Kategori BMI {{ $hasil->kategori->rentang() }} kg/m²
        </p>
    </div>
@endsection
