<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BahanStoreRequest extends FormRequest
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
            'kode_bahan' => 'required|unique:bahan_labs,kode_bahan',
            'nama_bahan' => 'required',
            'harga' => 'integer|min:0|nullable',
            'stok' => 'numeric|min:0|nullable',
            'min_stok' => 'numeric|min:0|nullable',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Harap mengisi :attribute',
            'kode_bahan.unique' => 'Kode bahan sudah terdaftar',
            'harga.integer' => 'Harga merupakan bilangan bulat',
            'harga.min' => 'Harga tidak boleh kurang dari 0',
            'stok.min' => 'Stok tidak boleh kurang dari 0',
            'min_stok.min' => 'Minimum stok tidak boleh kurang dari 0',
        ];
    }
}
