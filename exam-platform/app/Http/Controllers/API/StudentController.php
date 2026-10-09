<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\API\Student;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Http\Requests\Student\StoreRequest;
use App\Http\Requests\Student\UpdateRequest;
use App\Http\Resources\API\Student\ShowPanelResource;
use App\Http\Resources\API\Student\StudentCollection;

use Spatie\Permission\Commands\AssignRole;

class StudentController extends Controller
{

    // create Student
    public function store(StoreRequest $request){
        try{

            $Student = new Student();

            $user = auth()->user();

            if ($user->status == 3) {
                return ApiResponse::validationError('کاربر گرامی اطلاعات شما قبلا به عنوان دانشجو در سیستم ذخیره شده است.');
            }

            if ($user->status != 2 || $user->type != 1) {
                return ApiResponse::validationError('شما مجاز به ثبت اطلاعات خود به عنوان یک دانشجو نیستید. .');
            }

            $check = Student::where('user_id', $user->id)->first();
            if($check){
                return ApiResponse::validationError('کاربر با شماره تلفن وارد شده قبلا ثبت شده است.');
            }

            $Student->national_code = $request->national_code;
            $Student->student_code = $request->student_code;
            $Student->field_study = $request->field_study;
            $Student->education_level = $request->education_level;
            $Student->university = $request->university;       
            $Student->user_id =  $user->id;

            $Student->save();

            $user->assignRole('Student');
            $user->load('roles');

            $user->status = 3;
            $user->save();

            return ApiResponse::success();

        } catch (\Exception $e) {
            return ApiResponse::catch('store Student error', $e->getMessage());
        }
    }

    // update Student
    public function update(UpdateRequest $request){
        try{
            
            $Student = Student::find($request->id);

            if(!$Student){
                return ApiResponse::notFound(' تامین کننده');
            }

            
            // $user = User::where('mobile', $request->mobile)->first();

            // if(!$user){
            //     return ApiResponse::notFound(' کاربر با نام و شماره تلفن وارد شده ');
            // }


            if($request->type == 1){
                $Student->company_name = $request->company_name;
                $Student->economic_code = $request->economic_code;
                $Student->company_national_code = $request->company_national_code;
                $Student->registration_number = $request->registration_number;
                $Student->address = $request->address;
                if ($request->hasFile('user_national_code_image') && $request->file('user_national_code_image')->isValid()) {
                    $file = $request->file('user_national_code_image');
                    if ($file->getError()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'خطا در آپلود فایل: ' . $file->getErrorMessage()
                        ], 400);
                    }
                    
                    $extension = $file->getClientOriginalExtension();
                    $uniqName = rand(1000, 999999) . "_". rand(10000, 99999) . '_win24' . '.' . $extension;
                    
                    $file->move(public_path('images/Students'), $uniqName);
                    $Student->user_national_code_image = $uniqName;
                }
            } else {
                $Student->company_name = null;
                $Student->economic_code = null;
                $Student->company_national_code = null;
                $Student->registration_number = null;
                $Student->address = null;
                $Student->user_national_code_image = null;
            }
     
            $user = User::find($Student->user_id);
            // if($user->mobile != $request->mobile){
            //     $user->mobile = $request->mobile;
            // }
            $user->mobile = $request->mobile;
            $user->name = $request->user;
            $user->save();

            $Student->national_code = $request->national_code;
            $Student->mobile = $request->mobile;
            $Student->type = $request->type;

            $Student->save();

            return ApiResponse::success();

        } catch (\Exception $e) {
            return ApiResponse::catch('update Student error', $e->getMessage());
        }
    }

    public function showPanel(Request $request)
    {
        $Student = Student::where('id', $request->id)
            ->where('soft_delete', 0)
            ->first();

        if(!$Student){
            return ApiResponse::notFound(' تامین کننده');
        }

        return ApiResponse::success(new ShowPanelResource($Student));
    }


    // indexPanel
    public function indexPanel(Request $request) {
        try{
            $query = Student::where('soft_delete', 0);


            if ($request->filled('id')) {
                $name = $request->input('id');
                $query->where('id', 'LIKE', "%" . $name . "%");
            }
            
            if ($request->filled('user')) {
                $name = $request->input('user');
                $query->whereHas('user', function($q) use($name){
                    $q->where('name', 'like', "%{$name}%");
                });
                //  $q->where('name', 'like', "%{$userName}%");
            }
            
            if ($request->filled('user')) {
                $userName = $request->input('user');
                $query->whereHas('user', function ($q) use ($userName) {
                    $q->where('name', 'like', "%{$userName}%");
                });
            }

                        
            if ($request->filled('phone')) {
                $phone = $request->input('phone');
                $query->whereHas('user', function ($q) use ($phone) {
                    $q->where('mobile', 'like', "%{$phone}%");
                });
            }

            
            if ($request->filled('economic_code') && strlen($request->input('economic_code')) >= 3) {
                $economic_code = $request->input('economic_code');
                $query->where('economic_code', 'like', "%{$economic_code}%");
            }
            if ($request->filled('national_code') && strlen($request->input('national_code')) >= 3) {
                $national_code = $request->input('national_code');
                $query->where('national_code', 'like', "%{$national_code}%");
            }
            if ($request->filled('company_name') && strlen($request->input('company_name')) >= 3) {
                $company_name = $request->input('company_name');
                $query->where('company_name', 'like', "%{$company_name}%");
            }
            if ($request->filled('registration_number') && strlen($request->input('registration_number')) >= 3) {
                $registration_number = $request->input('registration_number');
                $query->where('registration_number', 'like', "%{$registration_number}%");
            }
            if ($request->filled('company_national_code') && strlen($request->input('company_national_code')) >= 3) {
                $company_national_code = $request->input('company_national_code');
                $query->where('company_national_code', 'like', "%{$company_national_code}%");
            }
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }
            if ($request->filled('type')) {
                $query->where('type', $request->input('type'));
            }
            
            // if ($request->filled('category')) {
            //     $name = $request->input('category');
            //     $query->whereHas('group', function ($q) use ($name) {  
            //         $q->where('name', 'LIKE', "%" . $name . "%");
            //     });
            // }

            
            // if ($request->filled('brand')) {
            //     $brand = $request->input('brand');
            //     $query->where('brand', 'like', "%{$brand}%");
            // }

            // if ($request->filled('inventory')) {
            //     $inventory = (int) $request->input('inventory');
            //     $query->where('inventory', $inventory);
            // }

            // if ($request->filled('warehouseInventory')) {
            //     $inventory = (int) $request->input('warehouseInventory');
            //     $query->where('warehouseInventory', 'like', "%{$inventory}%");
            // }




            // if ($request->filled('minSalesCount')) {
            //     $name = $request->input('minSalesCount');
            //     $query->where('ratio', 'LIKE', "%" . $name . "%");
            // }

            // if ($request->filled('price')) {
            //     $name = $request->input('price');
            //     $query->where('price', 'LIKE', "%" . $name . "%");
            // }

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

            $query->orderBy('id','DESC');


            $Students = $query->paginate(perPage: 20);
                
            return new StudentCollection($Students);
                
        } catch (\Exception $e) {
            return ApiResponse::catch('product list error', $e->getMessage());
        }
    }
}
