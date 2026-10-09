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
use App\Http\Requests\Question\StoreRequest;
use App\Http\Requests\Question\UpdateRequest;
use App\Http\Resources\API\Question\ShowPanelResource;
use App\Http\Resources\API\Exam\ExamCollection;
use App\Models\API\ExamQuestion;
use DB;
use Spatie\Permission\Commands\AssignRole;

class QuestionController extends Controller
{
    public function store(StoreRequest $request)
    {
        try {
        DB::beginTransaction();

            
            $user = auth()->user();

            $teacher = Teacher::where('user_id', $user->id)->first();

            if (!$teacher) {
                return ApiResponse::validationError('اطلاعات استاد پیدا نشد.');
            }

            $exam = Exam::find($request->exam_id);

            if ($exam->teacher_id != $teacher->id) {
                return ApiResponse::validationError('شما به این آزمون دسترسی ندارید.');
            }
            
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
                
                $examQuestion->score = $ques['score'];
                $examQuestion->exam_id = $exam->id;
                $examQuestion->question_id = $Question->id;

                $examQuestion->save();
            }


            DB::commit();

            return ApiResponse::success();

        } catch (\Exception $e) {

            DB::rollBack();

            return ApiResponse::catch(
                'store Question error',
                $e->getMessage()
            );
        }
    }
  
}
