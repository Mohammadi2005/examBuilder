<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasRules;
use App\Traits\HasMessages;
use App\Models\User;

class Exam extends Model
{
    use HasRules, HasMessages;
    
    protected $dateFormat = 'U'; 

    protected $fillable = [
        'teacher_id',
        'title',
        'description',
        'time',
        'status',
        'start_at',
        'end_at',
        'show_result',
        'random_questions',
        'random_options',
        'allow_review',
        'max_attempts',
        'created_at',
        'updated_at'
    ];

    
    public static $rules = [
        'title' => ['required','string','min:3','max:150'],
        'description' => ['required','string'],
        'time' => ['required','integer'],
        // 'status' => ['required','integer'],
        'start_at' =>  ['required','string'],
        'end_at' =>  ['required','string'],
        'show_result' => ['required','integer'],
        'random_questions' => ['required','integer'],
        'random_options' => ['required','integer'],
        'allow_review' => ['required','integer'],
        'max_attempts' => ['required','integer'],

        // 'Questions' => ['required', 'array', 'min:1'],
        // // 'Questions.*.id' => ['nullable','integer'],
        // 'Questions.*.ques_type' => ['required','integer'],
        // 'Questions.*.score' => ['required','numeric'],
        // 'Questions.*.resp_type' => ['required','integer'],
        // 'Questions.*.text' => ['nullable', 'required_if:ques_type,1,3','string'],
        // 'Questions.*.image' => [
        //     'nullable',
        //     'required_if:hasBtn,2,3',
        //     'image',
        //     'mimes:jpeg,png,jpg,gif,svg,webp',
        //     'max:5120'
        // ],
        // 'Questions.*.options' =>  ['nullable', 'required_if:resp_type,1','array', 'min:2'],
        // 'Questions.*.options.*.is_correct' =>  ['required','integer'],
        // 'Questions.*.options.*.value' => ['required','string'],
  
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
