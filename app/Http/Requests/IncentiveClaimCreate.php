<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IncentiveClaimCreate extends FormRequest
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
            'incentive_id'=>'bail|required|exists:incentives,id',
            'rank_id'=>'bail|required|exists:ranks,id'
        ];
    }
}
