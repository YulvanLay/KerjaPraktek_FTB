<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PeminjamanAlatStoreRequest extends FormRequest
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
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Harap memilih :attribute',
        ];
    }
}
