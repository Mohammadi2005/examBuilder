<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasRules;
use App\Traits\HasMessages;
use App\Models\User;

class ExamQuestion extends Model
{
    use HasRules, HasMessages;
    
    protected $dateFormat = 'U'; 

    protected $fillable = [
        'question_id',
        'exam_id',
        'created_at',
        'updated_at'
    ];

    public function question(){
        return $this->belongsTo(Question::class);
    }

    public function exam(){
        return $this->belongsTo(Exam::class);
    }
}
