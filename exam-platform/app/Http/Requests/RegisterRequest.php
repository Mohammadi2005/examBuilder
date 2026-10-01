<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;


class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|min:3',
            'mobile' => 'required|string|unique:users|regex:/^09[0-9]{9}$/',
            'gender' => 'required|in:0,1,2',
        ];
    }

    public function messages(): array {
        return [
            'name.required' => 'نام الزامیست',
            'mobile.required' => 'شماره تماس الزامیست',
            'gender.required'  => 'جنسیت الزامیست.'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => ' خطا اعتبارسنجی!',
            'statusCode' => 405,
            'errors' => $validator->errors()->first(),
            'data' => null
        ], 405));
    }
}
