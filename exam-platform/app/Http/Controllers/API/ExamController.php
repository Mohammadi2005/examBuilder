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
        // try{

            DB::beginTransaction();

            $Exam = new Exam();

            $user = auth()->user();

            $teacher = Teacher::where('user_id', $user->id)->first();
            if($user->status != 3){
                return ApiResponse::validationError('در حال حاضر شما دسترسی ثبت آزمون جدید ندارید.');
            }

            // dd($teacher->teacher_id);

            
            $Exam->teacher_id = $teacher->id;
            $Exam->title = $request->title;
            $Exam->description = $request->description ?? null;
            $Exam->time = $request->time;
            $Exam->status = 1;
            $Exam->start_at = $request->start_at;
            $Exam->end_at = $request->end_at;            
            $Exam->show_result =  $request->show_result;
            $Exam->random_questions =  $request->random_questions;
            $Exam->random_options =  $request->random_options;
            $Exam->allow_review =  $request->allow_review;
            $Exam->max_attempts =  $request->max_attempts;

            $Exam->save();

            foreach ($request->Questions as $ques) {

                $Question = new Question();
                
                $Question->teacher_id = $teacher->id;
                $Question->ques_type = $ques['ques_type'];
                if ($ques['ques_type'] == 1) {
                    $Question->text = $ques['text'];
                    $Question->image = null;
                } elseif ($ques['ques_type'] == 2) {
                    $Question->text = null;

                    if (isset($ques['image']) && $ques['image'] instanceof \Illuminate\Http\UploadedFile) {

                        if ($Question->image && file_exists(public_path('images/Question/' . $Question->image))) {
                            unlink(public_path('images/Question/' . $Question->image));
                        }

                        $file = $ques['image'];
                        $extension = $file->getClientOriginalExtension();
                        $uniqName = rand(1000, 999999) . "_" . rand(10000, 99999) . '_EP.' . $extension;
                        $file->move(public_path('images/Question'), $uniqName);
                        $Question->image = $uniqName;
                    }

                } elseif ($ques['ques_type'] == 3) {
                    $Question->text = $ques['text'];
                    
                    if (isset($ques['image']) && $ques['image'] instanceof \Illuminate\Http\UploadedFile) {
                    
                        if ($Question->image && file_exists(public_path('images/Question/' . $Question->image))) {
                            unlink(public_path('images/Question/' . $Question->image));
                        }
                        
                        $file = $ques['image'];
                        $extension = $file->getClientOriginalExtension();
                        $uniqName = rand(1000, 999999) . "_" . rand(10000, 99999) . '_EP.' . $extension;
                        $file->move(public_path('images/Question'), $uniqName);
                        $Question->image = $uniqName;
                    }
                }

                $Question->status = 1;
                $Question->score = $ques['score'];
                $Question->resp_type = $ques['resp_type'];
                $Question->save();
                
                if ($Question->resp_type == 1) {
                    // dd($ques['options']);
                    foreach ($ques['options'] as $opti) {

                        $option = new QuestionOption();
                        
                        $option->question_id = $Question->id;
                        $option->value = $opti['value'];
                        $option->is_correct = $opti['is_correct'];
                        
                        $option->save();
                    }
                }

                $examQuestion = new ExamQuestion();
                
                $examQuestion->exam_id = $Exam->id;
                $examQuestion->question_id = $Question->id;

                $examQuestion->save();
            }

            DB::commit();
            
            return ApiResponse::success();

        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return ApiResponse::catch('store Exam error', $e->getMessage());
        // }
    }
}