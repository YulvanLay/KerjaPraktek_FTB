<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KeperluanStoreRequest extends FormRequest
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
            'kode_keperluan' => 'required|unique:keperluans,kode_keperluan',
            'nama_keperluan' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Harap mengisi :attribute',
            'kode_keperluan.unique' => 'Kode bahan sudah terdaftar',
        ];
    }
}
