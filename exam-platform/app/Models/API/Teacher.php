<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasRules;
use App\Traits\HasMessages;
use App\Models\User;

class Teacher extends Model
{
    use HasRules, HasMessages;
    
    protected $dateFormat = 'U'; 

    protected $fillable = [
        'user_id',
        'national_code',
        'degree',
        'teacher_code',
        'field_study',
        'university',
        'bio',
        'image',
        'created_at',
        'updated_at'
    ];

    
    public static $rules = [
        'national_code' => ['required','string','unique:teachers', 'digits:10'],
        'teacher_code' => ['required','string','unique:teachers','digits:10'],
        'degree' => ['required','integer','in:1,2,3'],
        'field_study' => ['required','string'],
        'university' => ['required','string'],
        'bio' => ['required','string'],
        'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:5120'],        
    ];


    public function user(){
        return $this->belongsTo(User::class);
    }
}
