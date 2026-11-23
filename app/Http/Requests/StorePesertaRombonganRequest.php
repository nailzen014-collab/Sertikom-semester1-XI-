<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePesertaRombonganRequest extends FormRequest
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
            'sumber_data' => ['required', Rule::in(['manual', 'file'])],
            'peserta' => ['exclude_unless:sumber_data,manual', 'required', 'array'],
            'peserta.*.nama' => ['exclude_unless:sumber_data,manual', 'required', 'string', 'max:255'],
            'peserta.*.kelas' => ['exclude_unless:sumber_data,manual', 'nullable', 'string', 'max:100'],
            'file_peserta' => [
                'exclude_unless:sumber_data,file',
                'required',
                'file',
                'mimes:csv,txt,xlsx,xls',
                'extensions:csv,txt,xlsx,xls',
                'max:2048',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $draft = $this->session()->get('pengajuan_rombongan_draft');

                if (! $draft || $validator->errors()->any() || $this->input('sumber_data') !== 'manual') {
                    return;
                }

                $jumlahPeserta = count($this->input('peserta', []));

                if ($jumlahPeserta !== (int) $draft['jumlah_tiket']) {
                    $validator->errors()->add(
                        'peserta',
                        'Jumlah biodata harus sama dengan jumlah tiket yang diajukan.'
                    );
                }
            },
        ];
    }
}
