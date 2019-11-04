<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        switch ($this->method()) {
            case 'GET':
                {
                    return[

                    ];
                }
            case 'POST':
            {
                return[
                    'id'=>['exists']
                ];
            }
        }
        return [
            //
        ];

    }

    public function messages()
    {
        return [
            'id.required'=>'商品id必须填写',
            'id.exists'=>'该商品不存在',
            'name.unique'=>'该商品已存在',
            'name.required'=>'商品名不能为空',
        ];
    }

}
