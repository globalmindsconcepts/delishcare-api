<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
            'address'=>'bail|required|string|max:200',
            'gender'=>'bail|required|in:male,female',
            'image'=>'bail|nullable|image|mimes:png,jpg,jpeg|max:2048'
        ];
    }
}
