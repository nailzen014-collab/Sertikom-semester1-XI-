<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperadmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $admin = $this->route('admin');

        return [
            'nama' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($admin?->id),
            ],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'password' => [
                $admin ? 'nullable' : 'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ];
    }
}
