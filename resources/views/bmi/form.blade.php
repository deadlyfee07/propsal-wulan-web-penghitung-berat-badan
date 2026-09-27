@extends('layouts.app')

@section('title', 'Masukkan Data — BMI & Calorie Analyzer')

@section('content')
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <h2 class="text-lg font-semibold text-slate-900">Data Pengguna</h2>
        <p class="mt-1 text-sm text-slate-500">
            Lengkapi seluruh isian berikut. Tinggi badan diisi dalam sentimeter dan
            sistem akan otomatis mengubahnya ke meter saat perhitungan.
        </p>

        @if ($errors->any())
            <div role="alert"
                class="mt-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                <p class="font-semibold">Pesan Kesalahan — Validasi Data Gagal</p>
                <p class="mt-1">Periksa kembali isian yang ditandai berikut ini.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $pesan)
                        <li>{{ $pesan }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('bmi.hitung') }}" class="mt-6 space-y-5" novalidate>
            @csrf

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="usia" class="block text-sm font-medium text-slate-700">Usia</label>
                    <div class="mt-1.5">
                        <input type="number" name="usia" id="usia" inputmode="numeric" step="1"
                            value="{{ old('usia') }}" placeholder="17"
                            @error('usia') aria-invalid="true" aria-describedby="usia-error" @enderror
                            class="w-full rounded-lg border px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:ring-2 {{ $errors->has('usia') ? 'border-rose-400 bg-rose-50/40 focus:ring-rose-500/30' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500/30' }}">
                    </div>
                    @error('usia')
                        <p id="usia-error" class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-slate-400">Rentang yang diperbolehkan 2–120 tahun.</p>
                </div>

                <fieldset>
                    <legend class="text-sm font-medium text-slate-700">Jenis Kelamin</legend>
                    <div class="mt-3 grid grid-cols-2 gap-3">
                        @foreach ($jenisKelamin as $opsi)
                            <label class="flex cursor-pointer items-center gap-2.5 rounded-lg border px-3.5 py-2.5 text-sm transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-indigo-500/30 {{ $errors->has('jenis_kelamin') ? 'border-rose-400' : 'border-slate-300' }}">
                                <input type="radio" name="jenis_kelamin" value="{{ $opsi->value }}"
                                    @checked(old('jenis_kelamin', $loop->first ? $opsi->value : null) === $opsi->value)
                                    class="h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span>{{ $opsi->label() }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('jenis_kelamin')
                        <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </fieldset>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="berat_badan" class="block text-sm font-medium text-slate-700">Berat Badan (kg)</label>
                    <div class="mt-1.5">
                        <input type="number" name="berat_badan" id="berat_badan" inputmode="decimal" step="0.1"
                            value="{{ old('berat_badan') }}" placeholder="60"
                            @error('berat_badan') aria-invalid="true" aria-describedby="berat_badan-error" @enderror
                            class="w-full rounded-lg border px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:ring-2 {{ $errors->has('berat_badan') ? 'border-rose-400 bg-rose-50/40 focus:ring-rose-500/30' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500/30' }}">
                    </div>
                    @error('berat_badan')
                        <p id="berat_badan-error" class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tinggi_badan" class="block text-sm font-medium text-slate-700">Tinggi Badan (cm)</label>
                    <div class="mt-1.5">
                        <input type="number" name="tinggi_badan" id="tinggi_badan" inputmode="decimal" step="0.1"
                            value="{{ old('tinggi_badan') }}" placeholder="165"
                            @error('tinggi_badan') aria-invalid="true" aria-describedby="tinggi_badan-error" @enderror
                            class="w-full rounded-lg border px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:ring-2 {{ $errors->has('tinggi_badan') ? 'border-rose-400 bg-rose-50/40 focus:ring-rose-500/30' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500/30' }}">
                    </div>
                    @error('tinggi_badan')
                        <p id="tinggi_badan-error" class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-slate-400">Contoh: 165 cm = 1,65 m.</p>
                </div>
            </div>

            <div>
                <label for="aktivitas_fisik" class="block text-sm font-medium text-slate-700">Aktivitas Fisik</label>
                <div class="mt-1.5">
                    <select name="aktivitas_fisik" id="aktivitas_fisik"
                        @error('aktivitas_fisik') aria-invalid="true" aria-describedby="aktivitas_fisik-error" @enderror
                        class="w-full rounded-lg border px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:ring-2 {{ $errors->has('aktivitas_fisik') ? 'border-rose-400 bg-rose-50/40 focus:ring-rose-500/30' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500/30' }}">
                        <option value="">-- Pilih aktivitas --</option>
                        @foreach ($aktivitasFisik as $opsi)
                            <option value="{{ $opsi->value }}" @selected(old('aktivitas_fisik') === $opsi->value)>
                                {{ $opsi->label() }} (faktor {{ $opsi->faktorTerformat() }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('aktivitas_fisik')
                    <p id="aktivitas_fisik-error" class="mt-1.5 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center">
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-6 py-3 text-sm font-semibold tracking-wide text-white uppercase shadow-sm transition hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500/40 focus:outline-none focus:ring-offset-2">
                    Hitung
                </button>
                <p class="text-xs text-slate-500 sm:ml-2">
                    Input → Proses Validasi → Perhitungan BMI → Tampilan Hasil
                </p>
            </div>
        </form>
    </div>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white/70 p-6 sm:p-7">
        <h3 class="text-sm font-semibold text-slate-900">Kriteria BMI yang Digunakan</h3>
        <p class="mt-1 text-xs text-slate-500">Permenkes No. 2 Tahun 2025 untuk usia dewasa Berat badan (18 tahun ke atas).</p>
        <dl class="mt-4 grid gap-3 sm:grid-cols-2">
            @foreach (\App\Enums\KategoriBmi::cases() as $kategori)
                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <dt class="text-sm font-semibold text-slate-800">{{ $kategori->label() }}</dt>
                    <dd class="mt-0.5 text-xs text-slate-500">BMI {{ $kategori->rentang() }} kg/m²</dd>
                </div>
            @endforeach
        </dl>
    </section>
@endsection
