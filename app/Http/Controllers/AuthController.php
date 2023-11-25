<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;


class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json($user);
    }


    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();
        
        $token = $user->createToken('token')->plainTextToken;

        $cookie = cookie('jwt', $token, 24 * 60);

        return response()->json($user->email . ' logged in')->withCookie($cookie);
    }


    public function logout()
    {
        $cookie = Cookie::forget('jwt');

        return response()->json('You are logged out')->withCookie($cookie);
    }


    public function me()
    {
        return Auth::user();
    }
}
