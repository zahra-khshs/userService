<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify($token)
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid token'], 400);
        }

         $user->email_verified_at = now();
        $user->email_verification_token = null;
        $user->save();

        return response()->json(['message' => 'Email verified successfully.']);
    }}
