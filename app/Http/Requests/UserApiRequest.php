<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class UserApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        Validator::extend('has_uppercase', function ($attribute, $value) {
            return preg_match('/^.*[A-Z]+.*$/', $value);
        });

        Validator::extend('has_lowercase', function ($attribute, $value) {
            return preg_match('/^.*[a-z]+.*$/', $value);
        });

        Validator::extend('has_digit', function ($attribute, $value) {
            return preg_match('/^.*[0-9]+.*$/', $value);
        });

        Validator::extend('has_special_char', function ($attribute, $value) {
            return preg_match('/^.*[!@#$%^&*?]+.*$/', $value);
        });

        return [
            'username' => 'required|string|min:3|max:191',
            'password' => 'required|min:12|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!@#$%^&*?]).*$/'
        ];
    }

    /**
     * Set custom validation messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'password.min' => 'The password is too short. Not enough characters',
            'password.regex' => 'The password is not strong enough'
        ];
    }
}
