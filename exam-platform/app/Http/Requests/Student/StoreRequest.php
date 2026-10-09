<?php

namespace App\Http\Requests\Student;

use App\Models\API\Student;
use App\RestFullApi\ApiFormRequest;

class StoreRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return Student::rules();
    }
}
