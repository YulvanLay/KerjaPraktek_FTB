<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FasilitasUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ubah_nama_fasilitas' => 'required',
            'ubah_kode_laboratorium' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'ubah_nama_fasilitas.required' => 'Harap mengisi nama fasilitas',
            'ubah_kode_laboratorium.required' => 'Harap memilih laboratorium',
        ];
    }
}