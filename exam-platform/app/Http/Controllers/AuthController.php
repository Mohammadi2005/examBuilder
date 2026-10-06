<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\API\Teacher;
use App\Models\User;
use App\Models\Otp;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            return response()->json([
                'data' => null,
                'statusCode'=> 405,
                'success'=>false,
                'message' => 'کاربری با شماره وارد شده یافت نشد.',
            ], 405);  
        }
        
        if ($user->status == 0) {
            return response()->json([
                'data' => null,
                'statusCode'=> 405,
                'success'=>false,
                'message' => 'کاربر محترم ، حساب کاربری شما به حالت تعلیق در آمده است.',
            ], 405);            
        }

        $code = rand(100000, 999999);
        // $sms = new SmsService();
        // $sms->sendWithPattern($code, $request->phone);
        Otp::updateOrCreate(
            ['mobile' => $request->mobile],
            [
                'code' => $code,
                'f_name' => $user->f_name,
                'l_name' => $user->l_name,
                'type' => $user->type,
                'expires_at' => Carbon::now()->addMinutes(2),
                'user_id' => $user->id,
                'errors' => null
            ]
        );
        return response()->json([
            'data' => null,
            'code' => $code,
            'statusCode'=> 200,
            'success'=>true,
            'message' => 'رمز ورود به شماره شما ارسال شد',
            'errors' => null
        ]);
    }

    public function adminLogin(LoginRequest $request)
    {
        $user = User::with('roles')->where('mobile', $request->mobile)->first();

        $isAdmin = false;
        foreach ($user->roles as $role) {
            if($role->name == 'admin'){
                $isAdmin = true;
            }
        }

        if (!$isAdmin) {
            return response()->json([
                'data' => null,
                'statusCode'=> 405,
                'success'=>false,
                'message' => 'ادمین با شماره وارد شده یافت نشد.',
            ], 405);  
        }

        return $this->login($request);   
    }

    public function supplierLogin(LoginRequest $request)
    {
        $user = User::with('roles')->where('mobile', $request->mobile)->first();

        $isAdmin = false;
        foreach ($user->roles as $role) {
            if($role->name == 'supplier'){
                $isAdmin = true;
            }
        }

        if (!$isAdmin) {
            return response()->json([
                'data' => null,
                'statusCode'=> 405,
                'success'=>false,
                'message' => 'تامین کننده با شماره وارد شده یافت نشد.',
            ], 405);  
        }

        return $this->login($request);   
    }

    public function register(RegisterRequest $request)
    {
        $code = rand(100000, 999999);
   
        Otp::updateOrCreate(
            ['mobile' => $request->mobile],
            [
                'code' => $code,
                'f_name' => $request->f_name,
                'l_name' => $request->l_name,
                'type' => $request->type,
                'expires_at' => Carbon::now()->addMinutes(2),
                'errors'=> null
            ]
        );        

        return response()->json([
            'data' => null,
            'code' => $code,
            'statusCode' => 200,
            'success' => true,
            'message' => 'رمز ورود به شماره شما ارسال شد',
            'errors' => null
        ], 200);        
    }

    public function verifyOtp(Request $request)
    {
    
        $request->validate([
            'mobile' => 'required|string',
            'code' => 'required|string'
        ]);
        

        $otp = Otp::where('mobile', $request->mobile)->first();
        

        if (!$otp) {
            return response()->json([
                'data' => null,
                'statusCode' => 404, 
                'message' => 'کد تایید یافت نشد. لطفا دوباره درخواست دهید.',
                'success' => false,
                'errors' => null,
            ], 404);
        }
        
    
        if ($otp->isExpired()) {
            $otp->delete();
            return response()->json([
                'data' => null,
                'statusCode' => 410, 
                'message' => 'کد تایید منقضی شده است. لطفا دوباره درخواست دهید.',
                'success' => false,
                'errors' => null,
            ], 410);
        }
        
        if ($otp->code !== $request->code) {            
            return response()->json([
                'data' => null,
                'statusCode' => 405,
                'message' => "کد وارد شده اشتباه است.",
                'success' => false,
                'errors' => null
            ], 405);
        }
        
        if ($otp->user_id) {
            // $user = User::find($otp->user_id);
            $user = User::with('roles')->find($otp->user_id);
        } else {
            $user = new User();

            $user->f_name = $otp->f_name;
            $user->l_name = $otp->l_name;
            $user->mobile  = $otp->mobile;
            $user->type = $otp->type;
            $user->status = 1;
            $user->save();

            $user->assignRole('user');
            $user->load('roles');
        }

        $otp->delete();

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'data' => [
                'user' => $user,
                'token' => $token
            ],
            'statusCode' => 200,
            'message' => 'ورود با موفقیت انجام شد',
            'success' => true,
            'errors' => null
        ]);
    }
    public function getUser(Request $request)
    {
        try {
            $user = auth()->user();
            $roles = $user->getRoleNames();
            if (!$user) {
                return response()->json([
                    'data' => null,
                    'statusCode' => 402,
                    'success' => false,
                    'message' => 'توکن نامعتبر یا منقضی شده است.',
                    'errors' => null
                ], 402);
            }
            $data = [
                'user' => $user,
            ];
            if ($user->type == 2 and $user->status == 3) {
                $teacher = Teacher::where('user_id', $user->id)->first();
                $teacherData = [
                    "id" => $teacher->id,
                    "teacher_code" => $teacher->teacher_code,
                    "national_code" => $teacher->national_code,
                    "degree" => $teacher->degree,
                    "field_study" => $teacher->field_study,
                    "university" => $teacher->university,
                    "bio" => $teacher->bio,
                    "image" => asset('images/Teachers/' . $teacher->image),
                    "status" => $teacher->status,
                    "updated_at" => $teacher->updated_at
                ];
                $data['teacher'] = $teacherData;
            }

            return response()->json([
                'data' => $data,

                
                'statusCode' => 200,
                'success' => true,
                'message' => 'اطلاعات کاربر با موفقیت دریافت شد.',
                'errors' => null
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'data' => null,
                'statusCode' => 402,
                'success' => false,
                'message' => 'خطا در پردازش توکن.',
                'errors' => null
            ], 402);
        }
    }
}
