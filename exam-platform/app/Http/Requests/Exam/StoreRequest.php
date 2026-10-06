<?php

namespace App\Http\Requests\Exam;

use App\Models\API\Exam;
use App\RestFullApi\ApiFormRequest;

class StoreRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return Exam::rules();
    }
}
