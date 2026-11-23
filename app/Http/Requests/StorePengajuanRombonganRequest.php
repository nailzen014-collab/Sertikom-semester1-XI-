<?php

namespace App\Http\Requests;

use App\Models\PengajuanRombongan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePengajuanRombonganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tiket_paket_id' => ['required', 'integer', 'exists:tiket_paket,id'],
            'tanggal_kunjungan' => ['required', 'date', 'after_or_equal:today'],
            'jumlah_tiket' => ['required', 'integer', 'min:11'],
            'nama_sekolah' => ['required', 'string', 'max:255'],
            'penanggung_jawab' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:20'],
            'mode_tiket' => ['required', Rule::in([
                PengajuanRombongan::MODE_PER_PESERTA,
                PengajuanRombongan::MODE_PER_ROMBONGAN,
            ])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'jumlah_tiket.min' => 'Paket rombongan harus berisi lebih dari 10 peserta.',
            'nama_sekolah.required' => 'Nama sekolah wajib diisi.',
            'penanggung_jawab.required' => 'Nama penanggung jawab wajib diisi.',
        ];
    }
}
