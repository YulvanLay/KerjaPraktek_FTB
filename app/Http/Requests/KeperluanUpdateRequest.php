<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KeperluanUpdateRequest extends FormRequest
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
            'ubah_kode_keperluan' => 'required|unique:keperluans,kode_keperluan,'.$this->id.',kode_keperluan',
            'ubah_nama_keperluan' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'ubah_kode_keperluan.required' => 'Harap mengisi kode keperluan',
            'ubah_kode_bahan.unique' => 'Kode bahan sudah terdaftar',
            'ubah_nama_keperluan.required' => 'Harap mengisi nama keperluan',
        ];
    }
}
