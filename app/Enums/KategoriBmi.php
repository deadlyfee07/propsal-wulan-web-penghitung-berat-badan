<?php

namespace App\Enums;

/**
 * Kriteria BMI usia dewasa berat badan dalam (Permenkes No. 2 Tahun 2025).
 */
enum KategoriBmi: string
{
    case Kurang = 'kurang';
    case Normal = 'normal';
    case Lebih = 'lebih';
    case Obesitas = 'obesitas';

    public static function dari(float $bmi): self
    {
        return match (true) {
            $bmi < 18.5 => self::Kurang,
            $bmi < 25.0 => self::Normal,
            $bmi <= 27.0 => self::Lebih,
            default => self::Obesitas,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Kurang => 'Berat Badan Kurang',
            self::Normal => 'Berat Badan Normal',
            self::Lebih => 'Berat Badan Lebih',
            self::Obesitas => 'Obesitas',
        };
    }

    public function rentang(): string
    {
        return match ($this) {
            self::Kurang => '< 18,5',
            self::Normal => '18,5 – 24,9',
            self::Lebih => '25,0 – 27,0',
            self::Obesitas => '> 27,0',
        };
    }

    public function deskripsi(): string
    {
        return match ($this) {
            self::Kurang => 'BMI berada di bawah rentang normal.',
            self::Normal => 'BMI berada pada rentang normal.',
            self::Lebih => 'BMI berada di atas rentang normal.',
            self::Obesitas => 'BMI berada jauh di atas rentang normal.',
        };
    }
}
