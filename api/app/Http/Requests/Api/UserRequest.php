<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
//    /**
//     * Determine if the user is authorized to make this request.
//     *
//     * @return bool
//     */
//    public function authorize()
//    {
//        return true;
//    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if($this->path() == 'api/admin/login'){
            return [
                'name'=>['required','max:16','min:2'],
                'password'=>['required','max:16','min:6']
            ];
        }else{
            return [
                'name' =>['required','unique:pre_user,name','max:16','min:2'],
                'email'=>['required','unique:pre_user,email'],
                'password'=>['required','max:16','min:6'],
            ];
        }

    }
    public function messages()
    {
        return [
            'id.required'=>'用户id不能为空',
            'id.exists'=>'用户不存在',
            'name.unique'=>'用户已存在',
            'name.required'=>'用户名不能为空',
            'email.unique'=>'邮箱已存在',
            'email.required'=>'邮箱不能为空',
            'max'=>'长度不能超过规定值',
            'min'=>'长度不能低于规定值'
        ];
    }
}
