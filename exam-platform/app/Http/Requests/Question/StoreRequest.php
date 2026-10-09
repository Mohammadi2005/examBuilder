<?php

namespace App\Http\Requests\Question;

use App\Models\API\Question;
use App\RestFullApi\ApiFormRequest;

class StoreRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return Question::rules();
    }
}
