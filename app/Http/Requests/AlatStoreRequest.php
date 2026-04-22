<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AlatStoreRequest extends FormRequest
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
            'nama_alat' => 'required',
            'harga' => 'integer|min:0|nullable',
            'stok' => 'integer|min:0|nullable'
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Harap mengisi :attribute',
            'harga.integer' => 'Harga merupakan bilangan bulat',
            'harga.min' => 'Harga tidak boleh kurang dari 0',
            'stok.integer' => 'Stok merupakan bilangan bulat',
            'stok.min' => 'Stok tidak boleh kurang dari 0',
        ];
    }
}
