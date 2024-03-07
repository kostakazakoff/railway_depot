<?php

namespace App\Http\Controllers;

use App\Exceptions\AppException;
use App\Http\Filters\LogFilter;
use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    const SUCCESS = 'success';

    public function list(LogFilter $filter, Request $request)
    {
        $logs = Log::filter($filter)->get();

        $description = $request->query->get('description');

        if ($logs->isEmpty()) {
            return response()->json([
                'message' => AppException::notFound('записи')->getMessage(),
                'status' => AppException::notFound('записи')->getCode()
            ]);
        }

        $description &&
            $logs = $logs
            ->filter(function ($log) use ($description) {
                return (
                    strstr($log->created, $description) ||
                    strstr($log->updated, $description) ||
                    strstr($log->deleted, $description)
                );
            });

        return response()->json([
            'message' => self::SUCCESS,
            'logs' => [...$logs]
        ]);
    }

    public function delete(Request $request)
    {
        //TODO:
    }
}
