<?php

namespace App\Http\Controllers;

use App\Data\HasilPerhitungan;
use App\Enums\AktivitasFisik;
use App\Enums\JenisKelamin;
use App\Http\Requests\HitungBmiRequest;
use App\Services\BmiCalculator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BmiController extends Controller
{
    public const SESSION_KEY = 'hasil_perhitungan';

    public function __construct(private readonly BmiCalculator $calculator) {}

    public function form(Request $request): View
    {
        $request->session()->forget(self::SESSION_KEY);

        return view('bmi.form', [
            'jenisKelamin' => JenisKelamin::cases(),
            'aktivitasFisik' => AktivitasFisik::cases(),
        ]);
    }

    public function hitung(HitungBmiRequest $request): RedirectResponse
    {
        $request->session()->put(self::SESSION_KEY, $this->calculator->hitung(
            usia: $request->integer('usia'),
            jenisKelamin: JenisKelamin::from($request->string('jenis_kelamin')->toString()),
            beratBadan: (float) $request->input('berat_badan'),
            tinggiBadanCm: (float) $request->input('tinggi_badan'),
            aktivitasFisik: AktivitasFisik::from($request->string('aktivitas_fisik')->toString()),
        ));

        return redirect()->route('bmi.hasil');
    }

    public function hasil(Request $request): View|RedirectResponse
    {
        $hasil = $request->session()->get(self::SESSION_KEY);

        if (! $hasil instanceof HasilPerhitungan) {
            return redirect()->route('bmi.form');
        }

        return view('bmi.result', compact('hasil'));
    }
}
