<?php

namespace App\Http\Requests\Kategori;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateKategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;    // pastikan bernilai true karena otoritas sudah dihandle oleh middleware di route
    }

    public function rules(): array
    {
        $kategori = $this->route('kategori');
        return [
            'nama_kategori' => [
                'required',
                'string',
                'max:255',
                //mengembalikan pengecekan untuk ID kategori yang sedang di update
                Rule::unique('kategori', 'nama_kategori')->ignore($kategori),
            ],
        ];
    }
}
