<?php

namespace App\Http\Requests\Alat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAlatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $alatId = $this->route('alat');

        return [
            'kategori_id' => ['required', 'integer', 'exists:kategori,id'],
            'nama_alat' => ['required', 'string', 'max:255'],
            'kode_alat' => ['required', 'string', 'max:50', Rule::unique('alat', 'kode_alat')->ignore($alatId)],
            'stok' => ['required', 'integer', 'min:0'],
            'kondisi' => ['required', 'string', 'in:baik,rusak,perbaikan'],
            'deskripsi' => ['nullable', 'string'],
            'gambar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid.',
            'nama_alat.required' => 'Nama alat wajib diisi.',
            'kode_alat.required' => 'Kode alat wajib diisi.',
            'kode_alat.unique' => 'Kode alat sudah terdaftar.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.min' => 'Stok tidak boleh kurang dari 0.',
            'kondisi.required' => 'Kondisi alat wajib dipilih.',
            'kondisi.in' => 'Kondisi alat harus salah satu dari: baik, rusak, perbaikan.',
            'gambar.max' => 'Ukuran gambar maksimal 2 MB.',
            'gambar.image' => 'File yang diunggah harus berupa gambar.',
        ];
    }
}
