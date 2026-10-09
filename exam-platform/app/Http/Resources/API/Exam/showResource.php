<?php

namespace App\Http\Resources\API\Exam;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class showResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            "id"         => $this->id,
            "title"      => $this->title,
            "description"      => $this->description,
            "time"      => $this->time,
            "status"      => $this->status,
            "start_at"      => $this->start_at,
            "end_at"      => $this->end_at,
            "show_result"      => $this->show_result,
            "random_questions"      => $this->random_questions,
            "random_options"      => $this->random_options,
            "allow_review"      => $this->allow_review,
            "max_attempts"      => $this->max_attempts,
            'created_at' =>$this->updated_at,
        ];
    }
}
