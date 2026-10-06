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
        'text' => ['required','string','min:3','max:150'],
        'teacher_id' =>  ['required','integer','exists:teachers'],
        'ques_type' => ['required','integer'],
        'image' => ['required','image','mimes:jpeg,png,jpg,gif,svg,webp','max:5120'],
        'status' => ['required','integer'],
        'score' => ['required','numeric'],
        'resp_type' => ['required','integer'],
    ];

    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }
}
