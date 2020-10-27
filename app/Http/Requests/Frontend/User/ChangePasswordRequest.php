<?php

namespace App\Http\Requests\Frontend\User;

use App\Http\Requests\Request;

/**
 * Class ChangePasswordRequest
 * @package App\Http\Requests\Frontend\Access
 */
class ChangePasswordRequest extends Request
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
        // Validation rule for old password
        \Validator::extend('old_password', function ($attribute, $value, $parameters, $validator) {
          return \Hash::check($value, current($parameters));
        });

        return [
            'old_password' => 'required|old_password:' . \Auth::user()->password,
            'password'     => 'required|min:6|confirmed',
        ];


    }
}
