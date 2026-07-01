<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventarisUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ubah_nama_inventaris' => 'required',
            'ubah_jumlah' => 'integer|min:0|nullable',
            'ubah_harga_satuan' => 'integer|min:0|nullable',
            'ubah_tahun_pembelian' => 'integer|min:1900|max:' . (date('Y') + 1) . '|nullable',
            'ubah_mata_uang' => 'nullable|in:IDR,USD,YEN',
            'ubah_kode_laboratorium' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'ubah_nama_inventaris.required' => 'Harap mengisi nama inventaris',
            'ubah_jumlah.integer' => 'Jumlah merupakan bilangan bulat',
            'ubah_jumlah.min' => 'Jumlah tidak boleh kurang dari 0',
            'ubah_harga_satuan.integer' => 'Harga merupakan bilangan bulat',
            'ubah_harga_satuan.min' => 'Harga tidak boleh kurang dari 0',
            'ubah_tahun_pembelian.integer' => 'Tahun pembelian tidak valid',
            'ubah_mata_uang.in' => 'Mata uang tidak valid',
            'ubah_kode_laboratorium.required' => 'Harap memilih laboratorium',
        ];
    }
}