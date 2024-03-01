<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function delete_user(string $id)
    {
        $userToDelete = User::find($id);

        if ($userToDelete) {
            $userToDelete->delete();
            return response()->json(['message' => 'success']);
        }

        return response()->json(['message' => 'fail']);
    }


    public function users_list(): JsonResponse
    {
        $users = User::all()->load('profile');

        if (!$users) {
            return response()->json(['message' => 'fail']);
        }

        $result = [];

        foreach ($users as $user) {
            if ($user->role === 'superuser' || $user->id == auth()->user()->id) {
                continue;
            }
            $id = $user['id'];
            $result[$id] = $user;
        }

        return response()->json(['users' => $result, 'message' => 'success']);
    }


    public function edit_user(Request $request, string $id): JsonResponse
    {
        $user = User::find($id);
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

        foreach ($user_data as $field=>$value) {
            $user->$field = $value;
        }

        $user->save();

        foreach ($profile_data as $field=>$value) {
            $profile->$field = $value;
        }

        $profile->save();

        return response()->json(['profile' => $profile]);
    }
}
