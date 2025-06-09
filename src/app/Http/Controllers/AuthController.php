<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Registering a new user.
     *
     * @input Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'job_title' => 'required|string|max:255,',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $validatedData['password'] = bcrypt($validatedData['password']);

        $user = User::create($validatedData);
        $token = $user->createToken('access-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access-token' => $token,
            'token-type' => 'bearer token',
        ], 201);
    }

    /**
     * Logging in the user.
     *
     * @input Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($validatedData)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        } else {
            $user = User::where('email', $validatedData['email'])->first();
            $token = $user->createToken('access-token')->plainTextToken;

            return response()->json([
                'access-token' => $token,
                'token-type' => 'bearer token',
            ]);
        }
    }

    /**
     * Logging out the user.
     *
     * @return array<string, string>
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out',
        ];
    }
}
