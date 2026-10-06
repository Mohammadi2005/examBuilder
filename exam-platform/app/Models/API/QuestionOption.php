<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasRules;
use App\Traits\HasMessages;
use App\Models\User;

class QuestionOption extends Model
{
    use HasRules, HasMessages;
    
    protected $dateFormat = 'U'; 

    protected $fillable = [
        'question_id',
        'value',
        'is_correct',
        'created_at',
        'updated_at'
    ];

    public function question(){
        return $this->belongsTo(Question::class);
    }
}
