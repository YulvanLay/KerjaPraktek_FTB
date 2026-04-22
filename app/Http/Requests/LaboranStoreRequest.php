<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LaboranStoreRequest extends FormRequest
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
            'kode_laboran' => 'required|unique:laborans,kode_laboran',
            'nama_laboran' => 'required',
            'email' => 'email|nullable',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Harap mengisi :attribute',
            'kode_laboran.unique' => 'Kode laboran sudah terdaftar',
            'email' => 'Harap mengisi email dengan benar',
        ];
    }
}
