<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackageCreateRequest extends FormRequest
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
            'name'=>'bail|required|string|unique:packages,name',
            'vip'=>'bail|required|string|unique:packages,vip',
            'point_value'=>'bail|required|numeric',
            'value'=> 'bail|required|numeric',
            'registration_value'=>'bail|required|numeric',
            'profit_pool_eligible'=>'bail|nullable|in:true,false'
        ];
    }
}
