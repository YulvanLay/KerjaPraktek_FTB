<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PelangganRequest extends FormRequest
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
            'ubah_kode_pelanggan' => 'required|unique:pelanggans,kode_pelanggan,'.$this->id.',kode_pelanggan',
            'ubah_nama_pelanggan' => 'required',
            'ubah_email_pelanggan' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'ubah_kode_pelanggan.required' => 'Harap mengisi kode pejabat',
            'ubah_kode_pelanggan.unique' => 'Kode pejabat sudah terdaftar',
            'ubah_nama_pelanggan.required' => 'Harap mengisi nama pejabat',
            'ubah_email_pelanggan.required' => 'Harap mengisi email pejabat',
        ];
    }
}
