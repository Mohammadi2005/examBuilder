<?php

namespace App\Http\Resources\API\Exam;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'teacher' => $this->teacher->user->f_name . " " . $this->teacher->user->l_name,
            'title' => $this->title,
            'time' => $this->time,
            'show_result' => $this->show_result,
            'status' => $this->status,
            'created_at' => $this->created_at
        ];
    }
}
