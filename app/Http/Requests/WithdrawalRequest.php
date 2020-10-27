<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return \Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
      $method = $this->route()->parameter('method');

      if ($method == 'paypal') {
          return [
              'paypal_email' => 'sometimes|required|email'
          ];
      } elseif ($method == 'bank') {
          return [
              'bank_holder_name' => 'required|max:355',
              'bank_iban' => 'required|min:10|max:50',
              'bank_bic' => 'required|min:3|max:40',
              'bank_name' => 'required',
          ];
      } elseif ($method) {
          return [];
      }

      return [];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
