<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LaboranUpdateRequest extends FormRequest
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
            'ubah_kode_laboran' => 'required|unique:laborans,kode_laboran,'.$this->id.',kode_laboran',
            'ubah_nama_laboran' => 'required',
            'ubah_email' => 'email|nullable',
        ];
    }

    public function messages()
    {
        return [
            'ubah_kode_laboran.required' => 'Harap mengisi kode laboran',
            'ubah_kode_laboran.unique' => 'Kode laboran sudah terdaftar',
            'ubah_nama_laboran.required' => 'Harap mengisi nama laboran',
            'email' => 'Harap mengisi email dengan benar',
        ];
    }
}
