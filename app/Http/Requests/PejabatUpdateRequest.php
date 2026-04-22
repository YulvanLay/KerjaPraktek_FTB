<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PejabatUpdateRequest extends FormRequest
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
            'ubah_kode_pejabat' => 'required|unique:pejabat_strukturals,kode_pejabat,'.$this->id.',kode_pejabat',
            'ubah_nama_pejabat' => 'required',
            'ubah_jabatan' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'ubah_kode_pejabat.required' => 'Harap mengisi kode pejabat',
            'ubah_kode_pejabat.unique' => 'Kode pejabat sudah terdaftar',
            'ubah_nama_pejabat.required' => 'Harap mengisi nama pejabat',
            'ubah_jabatan.required' => 'Harap mengisi jabatan',
        ];
    }
}
