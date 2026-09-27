<?php

namespace App\Enums;

enum JenisKelamin: string
{
    case LakiLaki = 'laki-laki';
    case Perempuan = 'perempuan';

    public function label(): string
    {
        return match ($this) {
            self::LakiLaki => 'Laki-laki',
            self::Perempuan => 'Perempuan',
        };
    }

    /**
     * Konstanta pada formula Mifflin-St Jeor.
     */
    public function konstantaMifflin(): int
    {
        return match ($this) {
            self::LakiLaki => 5,
            self::Perempuan => -161,
        };
    }
}
