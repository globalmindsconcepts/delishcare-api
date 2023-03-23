<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
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
            'first_name'=>'bail|required|string',
            'last_name'=>'bail|required|string',
            'username'=>'bail|required|string|unique:users,username',
            'email'=>'bail|required|string|unique:users,email',
            'password'=>'bail|required|string',
            'referrer'=>'bail|required|exists:users,username',
            'package_id'=>'bail|required|exists:packages,id',
            'phone'=>'bail|required|string',
            'placer'=>'bail|nullable|string'
        ];
    }
}
