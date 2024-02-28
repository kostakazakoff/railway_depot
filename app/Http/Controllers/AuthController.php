<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;


class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:4'],
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'fix errors', 'errors' => $validator->errors()], 500);
        }

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json($user);
    }


    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password', 'role'))) {
            return response()->json(Response::HTTP_UNAUTHORIZED);
        }

        $user = auth()->user();

        $token = $user->createToken('token')->plainTextToken;

        $cookie = cookie('jwt', $token, 24 * 60, httpOnly: false);

        return response([
            'user' => $user,
            'jwt' => $token,
            'message' => 'success',
        ])->withCookie($cookie);
    }


    public function logout(Request $request): JsonResponse
    {
        $cookie = Cookie::forget('jwt');

        $request->user()->tokens()->delete();

        return response()->json(['message' => 'success'])->withCookie($cookie);
    }


    public function me()
    {
        return Auth::user();
    }

    // TODO: user_profile table && one2one relationship with user
    // Fields: First name, Last name, Phone
    public function edit_profile(): JsonResponse
    {
        return response()->json('Edit');
    }


    public function delete_user(string $id)
    {
        $userToDelete = User::find($id);

        if (
            $userToDelete
            && (auth()->user()->role === 'admin' || auth()->user()->role === 'superuser')
        ) {
            $userToDelete->delete();
            return response()->json(['message' => 'success']);
        }

        return response()->json(['message' => 'fail']);
    }


    public function delete_me(Request $request): JsonResponse
    {
        $user = auth()->user();

        if ($user) {
            $cookie = Cookie::forget('jwt');
            $user->delete();
            return response()->json(['message' => 'success'])->withCookie($cookie);
        }

        return response()->json(['message' => 'fail']);
    }
}
