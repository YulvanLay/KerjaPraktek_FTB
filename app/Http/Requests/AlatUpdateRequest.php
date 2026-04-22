<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AlatUpdateRequest extends FormRequest
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
            'ubah_nama_alat' => 'required',
            'ubah_harga' => 'integer|min:0|nullable',
            'ubah_stok' => 'integer|min:0|nullable'
        ];
    }

    public function messages()
    {
        return [
            'ubah_nama_alat.required' => 'Harap mengisi nama alat',
            'ubah_harga.integer' => 'Harga merupakan bilangan bulat',
            'ubah_harga.min' => 'Harga tidak boleh kurang dari 0',
            'ubah_stok.integer' => 'Stok merupakan bilangan bulat',
            'ubah_stok.min' => 'Stok tidak boleh kurang dari 0',
        ];
    }
}
