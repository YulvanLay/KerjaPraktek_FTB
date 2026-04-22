<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerekBahanUpdateRequest extends FormRequest
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
            'ubah_nama_merek' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'required' => 'Harap mengisi nama merek',
        ];
    }
}
