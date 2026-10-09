<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasRules;
use App\Traits\HasMessages;
use App\Models\User;

class Student extends Model
{
    use HasRules, HasMessages;
    
    protected $dateFormat = 'U'; 

    protected $fillable = [
        'user_id',
        'national_code',
        'student_code',
        'field_study',
        'education_level',
        'university',
        'created_at',
        'updated_at'
    ];

    
    public static $rules = [
        'national_code' => ['required','string','unique:Students', 'digits:10'],
        'student_code' => ['required','string','unique:Students','digits:10'],
        'education_level' => ['required','integer','in:1,2,3'],
        'field_study' => ['required','string'],
        'university' => ['required','string'],    
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
