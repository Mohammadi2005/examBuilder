<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasRules;
use App\Traits\HasMessages;
use App\Models\User;

class Question extends Model
{
    use HasRules, HasMessages;
    
    protected $dateFormat = 'U'; 

    protected $fillable = [
        'teacher_id',
        'ques_type',
        'text',
        'image',
        'status',
        'score',
        'resp_type',
        'created_at',
        'updated_at'
    ];

    
    public static $rules = [
        'Questions' => ['required', 'array', 'min:1'],
        // 'Questions.*.id' => ['nullable','integer'],
        'Questions.*.ques_type' => ['required','integer'],
        'Questions.*.score' => ['required','numeric'],
        'Questions.*.resp_type' => ['required','integer'],
        'Questions.*.text' => ['nullable', 'required_if:ques_type,1,3','string'],
        'Questions.*.image' => [
            'nullable',
            'required_if:hasBtn,2,3',
            'image',
            'mimes:jpeg,png,jpg,gif,svg,webp',
            'max:5120'
        ],
        'Questions.*.options' =>  ['nullable', 'required_if:resp_type,1','array', 'min:2'],
        'Questions.*.options.*.is_correct' =>  ['required','integer'],
        'Questions.*.options.*.value' => ['required','string'],
    ];

    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }
}
