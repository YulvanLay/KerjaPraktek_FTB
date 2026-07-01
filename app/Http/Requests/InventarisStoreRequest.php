<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventarisStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nama_inventaris' => 'required',
            'jumlah' => 'integer|min:0|nullable',
            'harga_satuan' => 'integer|min:0|nullable',
            'tahun_pembelian' => 'integer|min:1900|max:' . (date('Y') + 1) . '|nullable',
            'mata_uang' => 'nullable|in:IDR,USD,YEN',
            'kode_laboratorium' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Harap mengisi :attribute',
            'jumlah.integer' => 'Jumlah merupakan bilangan bulat',
            'jumlah.min' => 'Jumlah tidak boleh kurang dari 0',
            'harga_satuan.integer' => 'Harga merupakan bilangan bulat',
            'harga_satuan.min' => 'Harga tidak boleh kurang dari 0',
            'tahun_pembelian.integer' => 'Tahun pembelian tidak valid',
            'mata_uang.in' => 'Mata uang tidak valid',
            'kode_laboratorium.required' => 'Harap memilih laboratorium',
        ];
    }
}