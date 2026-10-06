<?php 
namespace App\Http\Responses;

class ApiResponse
{
    public static function success($data = null, string $message = 'موفقیت آمیز', int $statusCode = 200)
    {
        return response()->json([
            'data' => $data,
            'statusCode' => $statusCode,
            'success' => true,
            'message' => $message,
            'errors' => null
        ], $statusCode);
    }
    
    public static function error(string $message, $errors = null, int $statusCode = 400)
    {
        return response()->json([
            'data' => null,
            'statusCode' => $statusCode,
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }
    
    public static function validationError($errors, string $message = 'خطای اعتبارسنجی', int $statusCode = 405)
    {
        return response()->json([
            'data' => null,
            'statusCode' => $statusCode,
            'success' => false,
            'message' => $errors,
            'errors' => $errors
        ], $statusCode);
        // return self::error($message, $errors, $statusCode);
    }
    
    public static function notFound(string $name, int $statusCode = 402)
    {
        return response()->json([
            'statusCode' => $statusCode,
            'message' =>  $name . " مورد نظر وجود ندارد ",
            'success' => false,
        ], $statusCode);
    }
    public static function catch(string $message, string $e)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'error' => config('app.debug') ? $e : null
        ], 500);
    }
}