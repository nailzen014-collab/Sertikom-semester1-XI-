<?php

namespace App\Http\Requests\Admin;

use App\Models\TiketPaket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TiketPaketRequest extends FormRequest
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
            'kategori' => ['required', Rule::in(array_keys(TiketPaket::KATEGORI))],
            'deskripsi' => ['nullable', 'string'],
            'fasilitas' => ['nullable', 'string'],
            'harga' => ['required', 'integer', 'min:0'],
            'kuota_harian' => ['required', 'integer', 'min:0'],
            'status_aktif' => ['sometimes', 'boolean'],
        ];
    }
}
