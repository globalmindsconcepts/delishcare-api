<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Authenticate2FA extends FormRequest
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
            'code'=>'bail|required|string',
            'email'=>'bail|required|string',
            'user_type'=>'bail|required|string|in:user,admin',
            //'password'=>'bail|required|string'
        ];
    }
}
