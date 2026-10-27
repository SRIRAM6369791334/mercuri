<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Response;

class ResponseHelper
{
    // Helper function to return success response
    public static function successResponseData($status = 'success', $message = '', $data = null)
    {
        $response = [
            'status' => $status,
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return Response::json($response, 200);
    }

    // Helper function to return error response
    public static function errorResponse($status = "error", $message = '', $data = null)
    {
        return Response::json(['status' => $status, 'message' => $message, 'data' => $data]);
    }
    
    public static function successResponse($message)
    {
        return Response::json([
            "status" => "success",
            "message" => $message
        ]);
    }
}