<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePwRequest extends FormRequest
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
            'currentPassword' => 'required',
            'newPassword' => 'required',
            'confirmNew' => 'required|same:newPassword',
        ];
    }

    public function messages()
    {
        return [
            'currentPassword.required' => 'Harap mengisi kata sandi saat ini',
            'newPassword.required' => 'Harap mengisi kata sandi baru',
            'confirmNew.required' => 'Harap mengisi konfirmasi kata sandi',
            'confirmNew.same' => 'konfirmasi kata sandi tidak sesuai',
        ];
    }
}
