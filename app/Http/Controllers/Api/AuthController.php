<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::default()]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Registration failed. Please check your input.'
            ], 422);
        }

        // Buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        //Login user dan buat token
        Auth::login($user);
        $responseData = [
            'token' => $user->createToken($user->name)->plainTextToken,
            'name' => $user->name
        ];

        // Kirim response sukses
        return response()->json([
            'success' => true,
            'message' => 'User successfully registered.',
            'data' => $responseData,
        ]);
    }

    public function getUser(Request $request)
    {
        // Mengembalikan data pengguna yang sedang terotentikasi
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ], 200);
    }
}
