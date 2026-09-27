<?php

namespace App\Data;

use App\Enums\AktivitasFisik;
use App\Enums\JenisKelamin;
use App\Enums\KategoriBmi;

final readonly class HasilPerhitungan
{
    public function __construct(
        public int $usia,
        public JenisKelamin $jenisKelamin,
        public float $beratBadan,
        public float $tinggiBadan,
        public AktivitasFisik $aktivitasFisik,
        public float $tinggiMeter,
        public float $bmi,
        public KategoriBmi $kategori,
        public float $basalMetabolisme,
        public float $kebutuhanEnergi,
        public float $beratIdealMinimum,
        public float $beratIdealMaksimum,
    ) {}
}
