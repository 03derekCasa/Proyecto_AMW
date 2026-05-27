<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $normalizedUsername = Str::lower(
            ltrim(trim((string) $request->input('username')), '@')
        );

        $request->merge([
            'username' => $normalizedUsername,
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'artistic_name' => ['required', 'string', 'min:2', 'max:80'],

            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-z0-9._]+$/',
                'unique:users,username',
            ],

            'specialty' => ['nullable', 'string', 'max:255'],

            'email' => ['required', 'email', 'max:255', 'unique:users,email'],

            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'artistic_name.required' => 'El nombre artístico es obligatorio.',
            'username.required' => 'El apodo es obligatorio.',
            'username.min' => 'El apodo debe tener al menos 3 caracteres.',
            'username.max' => 'El apodo no puede superar los 30 caracteres.',
            'username.regex' => 'El apodo solo puede contener letras, números, puntos y guiones bajos.',
            'username.unique' => 'Ese apodo ya está en uso.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Profile::create([
            'user_id' => $user->id,
            'artistic_name' => $validated['artistic_name'],
            'specialty' => $validated['specialty'] ?? null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'profile' => $user->load('profile')->profile,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales no son correctas.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión correcto',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'profile' => $user->load('profile')->profile,
            ],
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('profile');

        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'profile' => $user->profile,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente',
        ]);
    }
}
