<?php

namespace App\Http\Requests;
class ChangePasswordRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'old_password' => 'required',
            'new_password' => 'required|min:8|regex:/^.*(?=.{8,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!@#$%^&*?-_+=]).*$/',
            'new_password_repeat' => 'required|same:new_password'
        ];
    }
    public function messages(): array
    {
        return [
            'new_password.min' => 'The new :attribute is too short. Please use at least 8 characters.',
            'new_password.regex' => 'Password is missing required characters: The password is not strong enough. The password must contain at least 1 capital letter (A-Z), 1 lowercase letter (a-z), 1 digit (0-9) and 1 special character (! @ # $ % ^ & * ?)'
        ];
    }
}
