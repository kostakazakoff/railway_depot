<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function edit_user_profile(): JsonResponse
    {
        //TODO:
        return response()->json('Edit user profile');
    }


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

        return response()->json(['users' => [...$users], 'message' => 'success']);
    }
}
