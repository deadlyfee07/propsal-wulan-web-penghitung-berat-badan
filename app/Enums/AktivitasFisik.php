<?php

namespace App\Enums;

enum AktivitasFisik: string
{
    case SangatRingan = 'sangat-ringan';
    case Ringan = 'ringan';
    case Sedang = 'sedang';
    case Aktif = 'aktif';
    case SangatAktif = 'sangat-aktif';

    public function label(): string
    {
        return match ($this) {
            self::SangatRingan => 'Sangat Ringan',
            self::Ringan => 'Ringan',
            self::Sedang => 'Sedang',
            self::Aktif => 'Aktif',
            self::SangatAktif => 'Sangat Aktif',
        };
    }

    /**
     * Physical Activity Level (PAL) yang dipakai untuk mengalikan basal metabolisme.
     */
    public function faktor(): float
    {
        return match ($this) {
            self::SangatRingan => 1.2,
            self::Ringan => 1.375,
            self::Sedang => 1.55,
            self::Aktif => 1.725,
            self::SangatAktif => 1.9,
        };
    }

    /**
     * Faktor aktivitas memakai pemisah desimal Indonesia tanpa nol di belakang.
     */
    public function faktorTerformat(): string
    {
        return rtrim(rtrim(number_format($this->faktor(), 3, ',', ''), '0'), ',');
    }

    public function deskripsi(): string
    {
        return match ($this) {
            self::SangatRingan => 'Duduk, membaca, atau kerja ringan tanpa gerakan berarti.',
            self::Ringan => 'Jalan kaki ringan 1–3 hari dalam seminggu.',
            self::Sedang => 'Olahraga ringan 3–5 hari dalam seminggu.',
            self::Aktif => 'Olahraga menengah hampir setiap hari.',
            self::SangatAktif => 'Olahraga berat setiap hari atau pekerjaan fisik berat.',
        };
    }
}
