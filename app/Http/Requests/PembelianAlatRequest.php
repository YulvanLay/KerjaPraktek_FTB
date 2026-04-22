<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PembelianAlatRequest extends FormRequest
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
            'no_PO' => 'required|unique:pembelian_alats,no_PO',
            'no_TTB' => 'required',
            'tanggal' => 'required',
            'laboran' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'no_PO.required' => 'Harap mengisi no PO',
            'no_TTB.required' => 'Harap mengisi no TTB',
            'tanggal.required' => 'Harap memilih tanggal pembelian',
            'laboran.required' => 'Harap memilih laboran penerima',
        ];
    }
}
