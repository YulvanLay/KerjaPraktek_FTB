<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PemakaianBahanRequest extends FormRequest
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
            'pelanggan' => 'required',
            'periode' => 'required',
            'keperluan' => 'required',
            'potongan' => 'integer|min:0|max:100|nullable',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Harap memilih :attribute',
            'potongan.min' => 'Nilai minimum potongan adalah 0',
            'potongan.max' => 'Nilai maksimum potongan adalah 100',
        ];
    }
}
