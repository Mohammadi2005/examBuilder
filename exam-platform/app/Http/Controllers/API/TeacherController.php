<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\API\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Http\Requests\Teacher\TeacherStoreRequest;
use App\Http\Requests\Teacher\TeacherUpdateRequest;
use App\Http\Resources\API\Teacher\ShowPanelResource;
use App\Http\Resources\API\Teacher\TeacherCollection;

use Spatie\Permission\Commands\AssignRole;

class TeacherController extends Controller
{

    // create Teacher
    public function store(TeacherStoreRequest $request){
        try{

            $Teacher = new Teacher();

            $user = auth()->user();

            if ($user->status == 3) {
                return ApiResponse::validationError('کاربر گرامی اطلاعات شما قبلا به عنوان استاد در سیستم ذخیره شده است.');
            }


            $check = Teacher::where('user_id', $user->id)->first();
            if($check){
                return ApiResponse::validationError('کاربر با شماره تلفن وارد شده قبلا ثبت شده است.');
            }


            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $uniqName = rand(1000, 999999) . "_". rand(10000, 99999) . '_EP' . '.' . $extension;
            $file->move(public_path('images/Teachers'), $uniqName);

            $Teacher->image = $uniqName;
            $Teacher->national_code = $request->national_code;
            $Teacher->teacher_code = $request->teacher_code;
            $Teacher->degree = $request->degree;
            $Teacher->field_study = $request->field_study;
            $Teacher->university = $request->university;
            $Teacher->bio = $request->bio;            
            $Teacher->user_id =  $user->id;


            $Teacher->save();

            $user->assignRole('Teacher');
            $user->load('roles');

            $user->status = 3;
            $user->save();

            return ApiResponse::success();

        } catch (\Exception $e) {
            return ApiResponse::catch('store Teacher error', $e->getMessage());
        }
    }

    // update Teacher
    public function update(TeacherUpdateRequest $request){
        try{
            
            $Teacher = Teacher::find($request->id);

            if(!$Teacher){
                return ApiResponse::notFound(' تامین کننده');
            }

            
            // $user = User::where('mobile', $request->mobile)->first();

            // if(!$user){
            //     return ApiResponse::notFound(' کاربر با نام و شماره تلفن وارد شده ');
            // }

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');
                if ($file->getError()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'خطا در آپلود فایل: ' . $file->getErrorMessage()
                    ], 400);
                }
                
                $extension = $file->getClientOriginalExtension();
                $uniqName = rand(1000, 999999) . "_". rand(10000, 99999) . '_win24' . '.' . $extension;
                
                $file->move(public_path('images/Teachers'), $uniqName);
                $Teacher->image = $uniqName;
            }



            if($request->type == 1){
                $Teacher->company_name = $request->company_name;
                $Teacher->economic_code = $request->economic_code;
                $Teacher->company_national_code = $request->company_national_code;
                $Teacher->registration_number = $request->registration_number;
                $Teacher->address = $request->address;
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
                    
                    $file->move(public_path('images/Teachers'), $uniqName);
                    $Teacher->user_national_code_image = $uniqName;
                }
            } else {
                $Teacher->company_name = null;
                $Teacher->economic_code = null;
                $Teacher->company_national_code = null;
                $Teacher->registration_number = null;
                $Teacher->address = null;
                $Teacher->user_national_code_image = null;
            }
     
            $user = User::find($Teacher->user_id);
            // if($user->mobile != $request->mobile){
            //     $user->mobile = $request->mobile;
            // }
            $user->mobile = $request->mobile;
            $user->name = $request->user;
            $user->save();

            $Teacher->national_code = $request->national_code;
            $Teacher->mobile = $request->mobile;
            $Teacher->type = $request->type;

            $Teacher->save();

            return ApiResponse::success();

        } catch (\Exception $e) {
            return ApiResponse::catch('update Teacher error', $e->getMessage());
        }
    }

    public function showPanel(Request $request)
    {
        $Teacher = Teacher::where('id', $request->id)
            ->where('soft_delete', 0)
            ->first();

        if(!$Teacher){
            return ApiResponse::notFound(' تامین کننده');
        }

        return ApiResponse::success(new ShowPanelResource($Teacher));
    }


    // indexPanel
    public function indexPanel(Request $request) {
        try{
            $query = Teacher::where('soft_delete', 0);


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


            $Teachers = $query->paginate(perPage: 20);
                
            return new TeacherCollection($Teachers);
                
        } catch (\Exception $e) {
            return ApiResponse::catch('product list error', $e->getMessage());
        }
    }
}
