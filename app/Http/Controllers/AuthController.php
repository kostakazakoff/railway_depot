<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
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

        $profile = Profile::create([
            'user_id' => $user->id
        ]);

        return response()->json($user);
    }


    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(Response::HTTP_UNAUTHORIZED);
        }

        $user = auth()->user();

        $profile = Profile::whereUserId($user->id)->first();

        $token = $user->createToken('token')->plainTextToken;

        $cookie = cookie('jwt', $token, 24 * 60, httpOnly: false);

        return response([
            'user' => $user,
            'jwt' => $token,
            'message' => 'success',
            'first_name' => $profile->first_name,
            'last_name' => $profile->last_name,
            'phone' => $profile->phone,
        ])->withCookie($cookie);
    }


    public function logout(Request $request): JsonResponse
    {
        $cookie = Cookie::forget('jwt');

        $request->user()->tokens()->delete();

        return response()->json(['message' => 'success'])->withCookie($cookie);
    }


    public function edit_my_profile(Request $request): JsonResponse
    {
        $user = auth()->user();

        $profile = Profile::whereUserId($user->id)->first();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid password']);
        }

        $user_data = [
            'email' => $request->email,
            'password' => $request->new_password ?
                Hash::make($request->new_password) :
                Hash::make($request->password)
        ];

        $profile_data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
        ];

        foreach ($user_data as $field => $value) {
            $user->$field = $value;
        }

        foreach ($profile_data as $field => $value) {
            $profile->$field = $value;
        }

        $user->save();
        $profile->save();

        return response()->json(['message' => 'success']);
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
