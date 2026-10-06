<?php

namespace App\Http\Requests\Teacher;

use App\Models\API\Teacher;
use App\RestFullApi\ApiFormRequest;

class TeacherStoreRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return Teacher::rules();
    }
}
