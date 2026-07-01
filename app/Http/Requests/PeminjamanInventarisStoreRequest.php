<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PeminjamanInventarisStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'pelanggan' => 'required',
            'periode' => 'required',
            'keperluan' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Harap memilih :attribute',
        ];
    }
}