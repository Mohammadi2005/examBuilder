<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserCollection;

class UserController extends Controller
{
    // list user 
    public function index(Request $request)
    {
        try{
            $query = User::query();

            if ($request->filled('id')) {
                $name = $request->input('id');
                $query->where('id', 'LIKE', "%" . $name . "%");
            }

            if ($request->filled('f_name') && strlen($request->input('f_name')) >= 3) {
                $f_name = $request->input('f_name');
                $query->where('f_name', 'LIKE', "%" . $f_name . "%");
            }

            if ($request->filled('l_name') && strlen($request->input('l_name')) >= 3) {
                $l_name = $request->input('l_name');
                $query->where('l_name', 'LIKE', "%" . $l_name . "%");
            }

            if ($request->filled('mobile')) {
                $mobile = $request->input('mobile');
                $query->where('mobile', 'LIKE', "%" . $mobile . "%");
            }          

            if ($request->filled('role')) {
                $roleName = $request->input('role');
                
                $query->whereHas('roles', function($q) use ($roleName) {
                    $q->where('name', $roleName);
                });
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
            return new UserCollection($users);
        } catch (\Exception $e) {
            return ApiResponse::catch('list users error', $e->getMessage());
        }
    }

    public function changeRole(Request $request){
        try{

            $user = User::find($request->id);
            if(!$user){
                return ApiResponse::notFound(' کاربر');
            }
            $user->syncRoles($request->role);            
            return ApiResponse::success();

        } catch (\Exception $e) {
            return ApiResponse::catch('change role error', $e->getMessage());
        }
    }

    public function changeStatus(Request $request){
        try{

            $user = User::find($request->id);
            if(!$user){
                return ApiResponse::notFound(' کاربر');
            }
            $user->status = $request->status;

            if ($request->status == 2) {
                if($user->type == 1){
                    $user->syncRoles('student'); 
                } elseif ($user->type == 2) {
                    $user->syncRoles('teacher'); 
                }
            }
 
            $user->save();
                     
            return ApiResponse::success();

        } catch (\Exception $e) {
            return ApiResponse::catch('change status error', $e->getMessage());
        }
    }
}
