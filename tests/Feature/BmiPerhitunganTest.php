<?php

namespace Tests\Feature;

use App\Data\HasilPerhitungan;
use App\Http\Controllers\BmiController;
use Tests\TestCase;

/**
 * Skenario pengujian pada BAB IV bagian F.
 */
class BmiPerhitunganTest extends TestCase
{
    /**
     * @return array<string, mixed>
     */
    private function dataValid(array $ubah = []): array
    {
        return array_merge([
            'usia' => 17,
            'jenis_kelamin' => 'laki-laki',
            'berat_badan' => 60,
            'tinggi_badan' => 165,
            'aktivitas_fisik' => 'sedang',
        ], $ubah);
    }

    public function test_halaman_utama_menampilkan_formulir(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('BMI & Calorie Analyzer')
            ->assertSee('Usia')
            ->assertSee('Jenis Kelamin')
            ->assertSee('Berat Badan (kg)')
            ->assertSee('Tinggi Badan (cm)')
            ->assertSee('Aktivitas Fisik');
    }

    public function test_halaman_utama_menampilkan_lima_pilihan_aktivitas_fisik(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Sangat Ringan')
            ->assertSee('Ringan')
            ->assertSee('Sedang')
            ->assertSee('Aktif')
            ->assertSee('Sangat Aktif');
    }

    /** Pengujian 1 — input lengkap, data valid, hasil muncul. */
    public function test_input_lengkap_menampilkan_hasil(): void
    {
        $this->from('/')->post('/hitung', $this->dataValid())->assertRedirect('/hasil');

        $this->get('/hasil')
            ->assertOk()
            ->assertSee('Hasil Perhitungan')
            ->assertSee('BMI Anda')
            ->assertSee('Kategori')
            ->assertSee('Estimasi Kebutuhan Energi');
    }

    /** Pengujian 2 — berat kosong. */
    public function test_berat_badan_kosong_menampilkan_pesan_kesalahan(): void
    {
        $this->from('/')
            ->post('/hitung', $this->dataValid(['berat_badan' => '']))
            ->assertRedirect('/')
            ->assertSessionHasErrors(['berat_badan' => 'Berat badan wajib diisi.']);

        $this->followingRedirects()
            ->from('/')
            ->post('/hitung', $this->dataValid(['berat_badan' => '']))
            ->assertSee('Validasi Data Gagal');
    }

    /** Pengujian 3 — tinggi kosong. */
    public function test_tinggi_badan_kosong_menampilkan_pesan_kesalahan(): void
    {
        $this->from('/')
            ->post('/hitung', $this->dataValid(['tinggi_badan' => '']))
            ->assertRedirect('/')
            ->assertSessionHasErrors(['tinggi_badan' => 'Tinggi badan wajib diisi.']);
    }

    /** Pengujian 4 — input negatif ditolak sistem. */
    public function test_berat_badan_negatif_ditolak(): void
    {
        $this->from('/')
            ->post('/hitung', $this->dataValid(['berat_badan' => -10]))
            ->assertRedirect('/')
            ->assertSessionHasErrors(['berat_badan' => 'Berat badan tidak boleh kurang dari 1 kg.']);
    }

    /** Pengujian 5 — BMI dihitung dari data valid. */
    public function test_bmi_dihitung_dengan_benar(): void
    {
        $this->post('/hitung', $this->dataValid());

        $this->get('/hasil')
            ->assertOk()
            ->assertSee('22,04')
            ->assertSee('kg/m²')
            ->assertSee('Berat Badan Normal');
    }

    /** Pengujian 6 — aktivitas dipilih, estimasi energi muncul. */
    public function test_estimasi_kebutuhan_energi_muncul(): void
    {
        $this->post('/hitung', $this->dataValid(['aktivitas_fisik' => 'aktif']));

        $this->get('/hasil')
            ->assertOk()
            ->assertSee('kkal/hari');

        $hasil = session(BmiController::SESSION_KEY);

        $this->assertInstanceOf(HasilPerhitungan::class, $hasil);

        // Basal metabolisme 1.551,25 kkal x faktor 1,725 = 2.676 kkal/hari.
        $this->assertSame(1551.3, $hasil->basalMetabolisme);
        $this->assertSame(2676.0, $hasil->kebutuhanEnergi);
    }

    public function test_halaman_hasil_mengalihkan_ke_formulir_bila_tidak_ada_data(): void
    {
        $this->get('/hasil')->assertRedirect('/');
    }

    public function test_tombol_hitung_ulang_menghapus_hasil_sebelumnya(): void
    {
        $this->post('/hitung', $this->dataValid());

        $this->get('/')->assertOk();

        $this->get('/hasil')->assertRedirect('/');
    }

    public function test_aktivitas_fisik_tidak_dikenal_ditolak(): void
    {
        $this->from('/')
            ->post('/hitung', $this->dataValid(['aktivitas_fisik' => 'ekstrem']))
            ->assertSessionHasErrors('aktivitas_fisik');
    }

    public function test_rentang_berat_ideal_muncul_pada_halaman_hasil(): void
    {
        $this->post('/hitung', $this->dataValid());

        $this->get('/hasil')
            ->assertOk()
            ->assertSee('Rentang Berat Ideal')
            ->assertSee('50,4')
            ->assertSee('67,8');
    }

    public function test_usia_di_luar_batas_ditolak(): void
    {
        $this->from('/')
            ->post('/hitung', $this->dataValid(['usia' => 0]))
            ->assertSessionHasErrors('usia');
    }
}
