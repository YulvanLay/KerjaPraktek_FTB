<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BahanUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   
        return [
            'ubah_kode_bahan' => 'required|unique:bahan_labs,kode_bahan,'.$this->id.',kode_bahan',
            'ubah_nama_bahan' => 'required',
            'ubah_harga' => 'integer|min:0|nullable',
            'ubah_stok' => 'numeric|min:0|nullable',
            'ubah_min_stok' => 'numeric|min:0|nullable'
        ];
    }

    public function messages()
    {
        return [
            'ubah_kode_bahan.required' => 'Harap mengisi kode bahan',
            'ubah_kode_bahan.unique' => 'Kode bahan sudah terdaftar',
            'ubah_harga.integer' => 'Harga merupakan bilangan bulat',
            'ubah_harga.min' => 'Harga tidak boleh kurang dari 0',
            'ubah_stok.min' => 'Stok tidak boleh kurang dari 0',
            'ubah_min_stok.min' => 'Minimum stok tidak boleh kurang dari 0',
        ];
    }
}
