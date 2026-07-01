<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FasilitasStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nama_fasilitas' => 'required',
            'kode_laboratorium' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'nama_fasilitas.required' => 'Harap mengisi nama fasilitas',
            'kode_laboratorium.required' => 'Harap memilih laboratorium',
        ];
    }
}