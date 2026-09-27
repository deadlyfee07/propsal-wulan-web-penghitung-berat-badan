<?php

namespace App\Http\Requests;

use App\Enums\AktivitasFisik;
use App\Enums\JenisKelamin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HitungBmiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'usia' => ['required', 'integer', 'min:2', 'max:120'],
            'jenis_kelamin' => ['required', Rule::enum(JenisKelamin::class)],
            'berat_badan' => ['required', 'numeric', 'min:1', 'max:500'],
            'tinggi_badan' => ['required', 'numeric', 'min:50', 'max:300'],
            'aktivitas_fisik' => ['required', Rule::enum(AktivitasFisik::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'usia.required' => 'Usia wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'berat_badan.required' => 'Berat badan wajib diisi.',
            'berat_badan.min' => 'Berat badan tidak boleh kurang dari 1 kg.',
            'berat_badan.max' => 'Berat badan tidak boleh lebih dari 500 kg.',
            'tinggi_badan.required' => 'Tinggi badan wajib diisi.',
            'tinggi_badan.min' => 'Tinggi badan tidak boleh kurang dari 50 cm.',
            'tinggi_badan.max' => 'Tinggi badan tidak boleh lebih dari 300 cm.',
            'aktivitas_fisik.required' => 'Tingkat aktivitas fisik wajib dipilih.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'usia' => 'usia',
            'jenis_kelamin' => 'jenis kelamin',
            'berat_badan' => 'berat badan',
            'tinggi_badan' => 'tinggi badan',
            'aktivitas_fisik' => 'aktivitas fisik',
        ];
    }
}
