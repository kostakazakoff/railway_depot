<?php

namespace App\Http\Controllers;

use App\Exceptions\AppException;
use App\Http\Filters\LogFilter;
use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    const SUCCESS = 'success';

    public function list(LogFilter $filter)
    {
        $logs = Log::filter($filter)->get();

        if ($logs->isEmpty()) {
            return response()->json([
                'message' => AppException::notFound('записи')->getMessage(),
                'status' => AppException::notFound('записи')->getCode()
            ]);
        }

        return response()->json([
            'message' => self::SUCCESS,
            'logs' => [...$logs]
        ]);
    }

    public function create(Request $request)
    {
        //TODO:
    }

    public function delete(Request $request)
    {
        //TODO:
    }
}
