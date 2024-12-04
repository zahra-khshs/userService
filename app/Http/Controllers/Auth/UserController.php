<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
         $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

         Cache::put("user:{$user->id}:session", [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ], now()->addMinutes(60));

         $token = FacadesJWTAuth::fromUser($user);

         $this->sendVerificationEmail($user);

        return response()->json([
            'message' => 'User successfully registered. Please verify your email.',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!$token = FacadesJWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'invalid_credentials'], 401);
        }


        Cache::put("user:{$request->email}:token", $token, now()->addMinutes(60));
        return response()->json(compact('token'));
    }

    public function profile(Request $request): JsonResponse
    {
         $user = Cache::get("user:{$request->user()->id}:session");

        if (!$user) {
             $user = $request->user();
            Cache::put("user:{$user->id}:session", $user, now()->addMinutes(60));
        }

        return response()->json($user);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

         $user->update($request->only('name', 'email'));

         Cache::put("user:{$user->id}:session", $user, now()->addMinutes(60));
        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ], 200);
    }
    protected function sendVerificationEmail(User $user)
    {
        \Mail::to($user->email)->send( new \App\Mail\VerificationEmail($user));
    }
}
