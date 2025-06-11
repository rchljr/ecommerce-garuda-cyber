<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    protected function sendResponse($result, $message = '', $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $result,
            'message' => $message,
        ], $code);
    }

    protected function sendError($error, $code = 400, $data = []): JsonResponse
    {
        $res = [
            'success' => false,
            'message' => $error,
        ];
        if (!empty($data)) {
            $res['data'] = $data;
        }
        return response()->json($res, $code);
    }
}
