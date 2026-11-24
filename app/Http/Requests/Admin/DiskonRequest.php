<?php

namespace App\Http\Requests\Admin;

use App\Models\Diskon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DiskonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'tiket_paket_id' => ['required', 'integer', Rule::exists('tiket_paket', 'id')],
            'tipe' => ['required', Rule::in(array_keys(Diskon::TIPE))],
            'nilai' => [
                'required',
                'integer',
                'min:1',
                $this->input('tipe') === Diskon::TIPE_PERSEN ? 'max:100' : 'max:100000000',
            ],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'status_aktif' => ['sometimes', 'boolean'],
            'ganti_diskon' => ['sometimes', 'boolean'],
        ];
    }
}
