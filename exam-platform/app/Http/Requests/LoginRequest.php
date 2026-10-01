<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mobile' => 'required|string|regex:/^09[0-9]{9}$/|exists:users,mobile',
        ];
    }

    public function messages(): array{
        return [
            'mobile.required' => 'شماره تلفن الزامی است',
            'mobile.regex' => 'فرمت شماره اشتباه است.',     
            'mobile.exists' => 'شماره تلفن وارد شده در سیستم ثبت نام نشده',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $validator->errors()->first(), 
            'statusCode' => 405,
            'errors' => $validator->errors()->first(),
            'data' => null
        ], 405));
    }
}
