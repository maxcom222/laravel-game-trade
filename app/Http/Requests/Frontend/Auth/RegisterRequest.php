<?php

namespace App\Http\Requests\Frontend\Auth;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;
use Config;

/**
 * Class RegisterRequest
 * @package App\Http\Requests\Frontend\Access
 */
class RegisterRequest extends Request
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
        $rules = [
            'name' => ['required', 'alpha_dash', 'min:3', 'max:35', Rule::unique('users')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')],
            'password' => 'required|min:6|confirmed'
        ];

        if (config('settings.recaptcha_register')) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        if (config('settings.register_checkbox')) {
            $rules['legal'] = 'required';
        }


        return $rules;
    }

	/**
     * @return array
     */
    public function messages() {
        return [
            'g-recaptcha-response.required_if' => trans('validation.required', ['attribute' => 'captcha']),
        ];
    }
}
