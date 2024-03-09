<?php

namespace App\Http\Controllers;

use App\Exceptions\AppException;
use App\Http\Filters\LogFilter;
use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

// TODO: Log creation events
class LogController extends Controller
{
    const SUCCESS = 'success';

    public function list(LogFilter $filter, Request $request): JsonResponse
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

    public function deleteOldLogs(): JsonResponse
    {
        $threeMonthsAgo = Carbon::now()->subMonths(3);

        $oldLogs = DB::table('logs')
        ->where('created_at', '<', $threeMonthsAgo)
        ->get();

        if ($oldLogs->isEmpty()) {
            return response()->json([
                'message' => 'There is no logs to delete'
            ]);
        }
        
        foreach ($oldLogs as $log) {
            $log->delete();
        }

        return response()->json([
            'message' => self::SUCCESS
        ]);
    }
}
