<?php

namespace App\Http\Controllers;

use App\Events\UserCRUD;
use App\Exceptions\AppException;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsersResponsibilitiesController extends Controller
{
    public function attachResponsibilities(Request $request, string $user_id): JsonResponse
    {
        $user = User::findOrFail($user_id);

        if ($user->role === 'member') {
            return response()->json([
                'message' => AppException::notActiveUser($user->email)->getMessage(),
                'status' => AppException::notActiveUser($user->email)->getCode()
            ]);
        }

        $user->stores()->syncWithoutDetaching($request->all());

        UserCRUD::dispatch($user, 'updated');

        return response()->json(['message' => 'success', 'user' => $user->load('stores')]);
    }


    public function detachResponsibilities(Request $request, string $user_id): JsonResponse
    {
        $user = User::findOrFail($user_id);

        $user->stores()->detach($request->all());

        UserCRUD::dispatch($user, 'updated');

        return response()->json(['message' => 'success', 'user' => $user->load('stores')]);
    }


    public function showResponsibilities($id): JsonResponse
    {
        $user = User::findOrFail($id)
            ->load('stores');

        return response()->json(['message' => 'success', 'responsibilities' => $user->stores]);
    }
}
