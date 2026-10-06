<?php

namespace App\Http\Requests\Teacher;

use App\Models\API\Teacher;
use App\RestFullApi\ApiFormRequest;
use Illuminate\Validation\Rule; 


class TeacherUpdateRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->input('id');
        return Teacher::rules([
            'image' => Rule::when($this->hasFile('image'), [
                    'required',
                    'image',
                    'mimes:jpeg,png,jpg,gif,svg,webp',
                    'max:5120'
                ], [
                    'nullable',
                    'string' 
                ]),

            'national_code' => [
                'required',
                'integer',
                'digits:10',
                Rule::unique('teachers', 'national_code')->ignore($id)
            ],
            'teacher_code' => [
                'required',
                'integer',
                'digits:10',
                Rule::unique('teachers', 'teacher_code')->ignore($id)
            ],
        ]);
    }
}
