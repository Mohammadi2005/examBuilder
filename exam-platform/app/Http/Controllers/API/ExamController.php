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
use App\Http\Resources\API\Exam\showResource;
use App\Models\API\ExamQuestion;
use DB;
use Spatie\Permission\Commands\AssignRole;

class ExamController extends Controller
{
    // list exam
    public function index(Request $request)
    {
        try{
            $query = Exam::query();

            if ($request->filled('id')) {
                $name = $request->input('id');
                $query->where('id', 'LIKE', "%" . $name . "%");
            }


            if ($request->filled('title') && strlen($request->input('title')) >= 3) {
                $title = $request->input('title');
                $query->where('title', 'LIKE', "%" . $title . "%");
            }

            if ($request->filled('time')) {
                $time = $request->input('time');
                $query->where('time', 'LIKE', "%" . $time . "%");
            }

            if ($request->filled('show_result')) {
                $show_result = $request->input('show_result');
                $query->where('show_result', 'LIKE', "%" . $show_result . "%");
            }          

            if ($request->filled('status')) {
                $status = $request->input('status');
                $query->where('status', 'LIKE', "%" . $status . "%");
            }          

            if($request->filled('start_date') || $request->filled('end_date')){
                if ($request->filled('start_date') && $request->filled('end_date')){
                    $query->whereBetween('created_at', [
                        $request->input('start_date'),
                        $request->input('end_date')
                    ]);
                } elseif ($request->filled('start_date')) {
                    $query->where('created_at', '>=', $request->input('start_date'));
                } elseif ($request->filled('end_date')) {
                    $query->where('created_at', '<=', $request->input('end_date'));
                }
            }


            $users = $query->orderBy('id', 'desc')->paginate(20);
            return new ExamCollection($users);
        } catch (\Exception $e) {
            return ApiResponse::catch('list users error', $e->getMessage());
        }
    }

    // create Exam
    public function store(StoreRequest $request){
        try {

            DB::beginTransaction();

            $user = auth()->user();

            if ($user->status != 3) {
                return ApiResponse::validationError('در حال حاضر شما دسترسی ثبت آزمون جدید ندارید.');
            }

            $teacher = Teacher::where('user_id', $user->id)->first();

            if (!$teacher) {
                return ApiResponse::validationError('اطلاعات استاد پیدا نشد.');
            }

            $exam = new Exam();

            $exam->teacher_id = $teacher->id;
            $exam->title = $request->title;
            $exam->description = $request->description ?? null;
            $exam->time = $request->time;
            $exam->status = 1;
            $exam->start_at = $request->start_at;
            $exam->end_at = $request->end_at;
            $exam->show_result = $request->show_result;
            $exam->random_questions = $request->random_questions;
            $exam->random_options = $request->random_options;
            $exam->allow_review = $request->allow_review;
            $exam->max_attempts = $request->max_attempts;

            $exam->save();

            DB::commit();

            return ApiResponse::success([
                'exam_id' => $exam->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::catch('store Exam error',$e->getMessage());
        }
    }

    // update Exam
    public function update(UpdateRequest $request){
        try {

            DB::beginTransaction();

            $user = auth()->user();

            $exam = Exam::find($request->id);

            if (!$exam) {
                return ApiResponse::notFound('آزمون');
            }

            $teacher = Teacher::where('user_id', $user->id)->first();

            if (!$teacher) {
                return ApiResponse::validationError('اطلاعات استاد پیدا نشد.');
            }

            if ($user->status != 3 || $exam->teacher_id != $teacher->id) {
                return ApiResponse::validationError('در حال حاضر شما دسترسی ویرایش آزمون ندارید.');
            }


            $exam->title = $request->title;
            $exam->description = $request->description ?? null;
            $exam->time = $request->time;
            $exam->status = 1;
            $exam->start_at = $request->start_at;
            $exam->end_at = $request->end_at;
            $exam->show_result = $request->show_result;
            $exam->random_questions = $request->random_questions;
            $exam->random_options = $request->random_options;
            $exam->allow_review = $request->allow_review;
            $exam->max_attempts = $request->max_attempts;

            $exam->save();

            DB::commit();

            return ApiResponse::success([
                'exam_id' => $exam->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::catch('update Exam error',$e->getMessage());
        }
    }

    // show Exam
    public function show(Request $request) {
        try {
            $exam = Exam::where('id',$request->id)->where('soft_delete',0)->first();
            return ApiResponse::success(new showResource($exam));
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::catch('show Exam error',$e->getMessage());
        }
    }

    public function changeStatus(Request $request){
        try{

            $exam = Exam::find($request->id);
            if(!$exam){
                return ApiResponse::notFound('آزمون');
            }

            $exam->status = $request->status;
            $exam->save();

            return ApiResponse::success();

        } catch (\Exception $e) {
            return ApiResponse::catch('change status error', $e->getMessage());
        }
    }
}