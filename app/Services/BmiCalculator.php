<?php

namespace App\Services;

use App\Data\HasilPerhitungan;
use App\Enums\AktivitasFisik;
use App\Enums\JenisKelamin;
use App\Enums\KategoriBmi;

class BmiCalculator
{
    public const BMI_NORMAL_MIN = 18.5;

    public const BMI_NORMAL_MAX = 24.9;

    /**
     * Tahap 8-11 algoritma: konversi tinggi, hitung BMI, tentukan kategori,
     * lalu hitung estimasi kebutuhan energi.
     */
    public function hitung(
        int $usia,
        JenisKelamin $jenisKelamin,
        float $beratBadan,
        float $tinggiBadanCm,
        AktivitasFisik $aktivitasFisik,
    ): HasilPerhitungan {
        $tinggiMeter = round($tinggiBadanCm / 100, 2);
        $bmi = round($beratBadan / ($tinggiMeter ** 2), 2);
        $basalMetabolisme = $this->hitungBasalMetabolisme($usia, $jenisKelamin, $beratBadan, $tinggiBadanCm);

        return new HasilPerhitungan(
            usia: $usia,
            jenisKelamin: $jenisKelamin,
            beratBadan: $beratBadan,
            tinggiBadan: $tinggiBadanCm,
            aktivitasFisik: $aktivitasFisik,
            tinggiMeter: $tinggiMeter,
            bmi: $bmi,
            kategori: KategoriBmi::dari($bmi),
            basalMetabolisme: round($basalMetabolisme, 1),
            kebutuhanEnergi: round($basalMetabolisme * $aktivitasFisik->faktor()),
            beratIdealMinimum: round(self::BMI_NORMAL_MIN * $tinggiMeter ** 2, 1),
            beratIdealMaksimum: round(self::BMI_NORMAL_MAX * $tinggiMeter ** 2, 1),
        );
    }

    /**
     * Formula Mifflin-St Jeor.
     */
    public function hitungBasalMetabolisme(
        int $usia,
        JenisKelamin $jenisKelamin,
        float $beratBadan,
        float $tinggiBadanCm,
    ): float {
        return (10 * $beratBadan)
            + (6.25 * $tinggiBadanCm)
            - (5 * $usia)
            + $jenisKelamin->konstantaMifflin();
    }
}
