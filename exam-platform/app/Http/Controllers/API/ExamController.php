<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\API\Exam;
use App\Models\API\Question;
use App\Models\API\QuestionOption;
use App\Models\API\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Http\Requests\Exam\StoreRequest;
use App\Http\Requests\Exam\UpdateRequest;
use App\Http\Resources\API\Exam\ShowPanelResource;
use App\Http\Resources\API\Exam\ExamCollection;
use App\Models\API\ExamQuestion;
use DB;
use Spatie\Permission\Commands\AssignRole;

class ExamController extends Controller
{

    // create Exam
    public function store(StoreRequest $request){
        try{

            DB::beginTransaction();

            $Exam = new Exam();

            $user = auth()->user();

            $teacher = Teacher::where('user_id', $user->id)->first();
            if($teacher->status != 3){
                return ApiResponse::validationError('در حال حاضر شما دسترسی ثبت آزمون جدید ندارید.');
            }

            $Exam->teacher_id = $teacher->teacher_id;
            $Exam->title = $request->title;
            $Exam->description = $request->description ?? null;
            $Exam->time = $request->time;
            $Exam->status = 1;
            $Exam->start_at = $request->start_at;
            $Exam->end_at = $request->end_at;            
            $Exam->show_result =  $user->show_result;
            $Exam->random_questions =  $user->random_questions;
            $Exam->random_options =  $user->random_options;
            $Exam->allow_review =  $user->allow_review;
            $Exam->max_attempts =  $user->max_attempts;

            $Exam->save();


            DB::commit();
            
            return ApiResponse::success();

        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::catch('store Exam error', $e->getMessage());
        }
    }
}