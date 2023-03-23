<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IncentiveCreateRequest extends FormRequest
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
            'rank_id'=>'bail|required|exists:ranks,id',
            'incentive'=>'bail|nullable|string',
            'worth'=>'bail|required|numeric',
            'image'=>'bail|nullable'
        ];
    }
}
