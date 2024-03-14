<?php

namespace App\Http\Controllers;

use App\Exceptions\AppException;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    const SUCCESS = 'success';

    public function delete_user(string $id)
    {
        $userToDelete = User::find($id);

        if (!$userToDelete) {
            return response()->json([
                'message' => AppException::notFound('такъв потребител')->getMessage(),
                'status' => AppException::notFound('такъв потребител')->getCode()
            ]);
        }


        $userToDelete->delete();
        return response()->json(['message' => self::SUCCESS]);
    }


    public function users_list(): JsonResponse
    {
        $users = User::all()->load('profile')->load('stores');

        if (!$users) {
            return response()->json([
                'message' => AppException::notFound('потребители')->getMessage(),
                'status' => AppException::notFound('потребители')->getCode()
            ]);
        }

        $result = [];

        foreach ($users as $user) {
            if ($user->id == auth()->user()->id) {
                continue;
            }
            $id = $user['id'];
            $result[$id] = $user;
        }

        return response()->json(['users' => $result, 'message' => self::SUCCESS]);
    }


    public function edit_user(Request $request, string $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => AppException::notFound('такъв потребител')->getMessage(),
                'status' => AppException::notFound('такъв потребител')->getCode()
            ]);
        }

        $profile = Profile::whereUserId($id)->first();

        $user_data = [
            'email' => $request->email,
            'role' => $request->role,
        ];

        $profile_data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
        ];

        foreach ($user_data as $field => $value) {
            $user->$field = $value;
        }

        $user->save();

        foreach ($profile_data as $field => $value) {
            $profile->$field = $value;
        }

        $profile->save();

        return response()->json(['message' => self::SUCCESS, 'profile' => $profile]);
    }
}
