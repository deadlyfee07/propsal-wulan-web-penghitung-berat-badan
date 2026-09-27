<?php

namespace Tests\Unit;

use App\Enums\AktivitasFisik;
use App\Enums\JenisKelamin;
use App\Enums\KategoriBmi;
use App\Services\BmiCalculator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BmiCalculatorTest extends TestCase
{
    private BmiCalculator $kalkulator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kalkulator = new BmiCalculator;
    }

    /**
     * Tabel "Contoh Data Pengujian" pada BAB IV bagian E.
     */
    #[DataProvider('dataContohPengujian')]
    public function test_bmi_sesuai_contoh_pengujian(int $usia, float $berat, float $tinggi, AktivitasFisik $aktivitas, float $bmiHarapan): void
    {
        $hasil = $this->kalkulator->hitung($usia, JenisKelamin::LakiLaki, $berat, $tinggi, $aktivitas);

        $this->assertSame($bmiHarapan, $hasil->bmi);
    }

    /**
     * @return array<string, array{int, float, float, AktivitasFisik, float}>
     */
    public static function dataContohPengujian(): array
    {
        return [
            'No. 1 — 17 tahun, 50 kg, 160 cm, ringan' => [17, 50.0, 160.0, AktivitasFisik::Ringan, 19.53],
            'No. 2 — 17 tahun, 60 kg, 165 cm, sedang' => [17, 60.0, 165.0, AktivitasFisik::Sedang, 22.04],
            'No. 3 — 18 tahun, 70 kg, 170 cm, aktif' => [18, 70.0, 170.0, AktivitasFisik::Aktif, 24.22],
            'No. 4 — 18 tahun, 80 kg, 175 cm, sedang' => [18, 80.0, 175.0, AktivitasFisik::Sedang, 26.12],
        ];
    }

    public function test_tinggi_badan_dikonversi_dari_sentinel_ke_meter(): void
    {
        $hasil = $this->kalkulator->hitung(17, JenisKelamin::LakiLaki, 60.0, 165.0, AktivitasFisik::Sedang);

        $this->assertSame(1.65, $hasil->tinggiMeter);
    }

    #[DataProvider('dataBatasKategori')]
    public function test_penentuan_kategori_berdasarkan_bmi(float $bmi, KategoriBmi $harapan): void
    {
        $this->assertSame($harapan, KategoriBmi::dari($bmi));
    }

    /**
     * @return array<string, array{float, KategoriBmi}>
     */
    public static function dataBatasKategori(): array
    {
        return [
            'di bawah 18,5' => [18.4, KategoriBmi::Kurang],
            'tepat 18,5' => [18.5, KategoriBmi::Normal],
            'di bawah 25' => [24.99, KategoriBmi::Normal],
            'tepat 25' => [25.0, KategoriBmi::Lebih],
            'tepat 27' => [27.0, KategoriBmi::Lebih],
            'di atas 27' => [27.01, KategoriBmi::Obesitas],
        ];
    }

    public function test_kebutuhan_energi_laki_laki_menggunakan_mifflin_st_jeor(): void
    {
        $hasil = $this->kalkulator->hitung(30, JenisKelamin::LakiLaki, 80.0, 180.0, AktivitasFisik::SangatRingan);

        // (10 x 80) + (6,25 x 180) - (5 x 30) + 5 = 1780 kkal basal
        $this->assertSame(1780.0, $hasil->basalMetabolisme);

        // 1780 x 1,2 = 2136 kkal/hari
        $this->assertSame(2136.0, $hasil->kebutuhanEnergi);
    }

    public function test_kebutuhan_energi_perempuan_menggunakan_mifflin_st_jeor(): void
    {
        $hasil = $this->kalkulator->hitung(30, JenisKelamin::Perempuan, 65.0, 165.0, AktivitasFisik::Ringan);

        // (10 x 65) + (6,25 x 165) - (5 x 30) - 161 = 1370,3 kkal basal
        $this->assertSame(1370.3, $hasil->basalMetabolisme);

        // 1370,3 x 1,375 = 1884 kkal/hari
        $this->assertSame(1884.0, $hasil->kebutuhanEnergi);
    }

    #[DataProvider('dataFaktorAktivitas')]
    public function test_semua_lima_tingkat_aktivitas_fisik_tersedia(AktivitasFisik $aktivitas, float $faktor): void
    {
        $this->assertSame($faktor, $aktivitas->faktor());
    }

    /**
     * @return array<string, array{AktivitasFisik, float}>
     */
    public static function dataFaktorAktivitas(): array
    {
        return [
            'sangat ringan' => [AktivitasFisik::SangatRingan, 1.2],
            'ringan' => [AktivitasFisik::Ringan, 1.375],
            'sedang' => [AktivitasFisik::Sedang, 1.55],
            'aktif' => [AktivitasFisik::Aktif, 1.725],
            'sangat aktif' => [AktivitasFisik::SangatAktif, 1.9],
        ];
    }

    public function test_rentang_berat_ideal_dihitung_dari_bmi_normal(): void
    {
        $hasil = $this->kalkulator->hitung(17, JenisKelamin::LakiLaki, 60.0, 165.0, AktivitasFisik::Sedang);

        // 18,5 x 1,65^2 = 50,4 kg dan 24,9 x 1,65^2 = 67,8 kg
        $this->assertSame(50.4, $hasil->beratIdealMinimum);
        $this->assertSame(67.8, $hasil->beratIdealMaksimum);
    }
}
