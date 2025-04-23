<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email|min:3|max:64',
            'password' => 'required|string|min:8|max:64|regex:/^\S*$/u|confirmed',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'User'
        ]);

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|min:3|max:64',
            'password' => 'required|string|min:8|max:64|regex:/^\S*$/u'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if ($user->role === 'Admin') {
            return response()->json(['message' => 'Welcome to Dashboard'], 200);
        }

        return response()->json(['message' => 'Welcome to Home'], 200);
    }

    public function uploadProfilePicture(Request $request, $id)
{
    $user = User::findOrFail($id);

    $request->validate([
        'profile_picture' => 'required|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    if ($request->hasFile('profile_picture')) {
        // Optional: delete old picture
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $path = $request->file('profile_picture')->store('profile_pictures', 'public');
        $user->profile_picture = $path;
        $user->save();
    }

        return response()->json([
            'message' => 'Profile picture updated successfully.',
            'user' => $user
        ]); 
    }
}
       // public function register(Request $request)
        // {
        //     $request->validate([
        //         'name' => 'required',
        //         'email' => 'required|email|unique:users',
        //         'password' => 'required|min:6',
        //         'role' => 'in:User,Admin'
        //     ]);

        //     $user = User::create([
        //         'name' => $request->name,
        //         'email' => $request->email,
        //         'password' => Hash::make($request->password),
        //         'role' => $request->role ?? 'User'
        //     ]);

        //     return response()->json(['message' => 'User registered successfully', 'user' => $user]);
        // }
        // public function login(Request $request)
        // {
        //     if (Auth::attempt($request->only('email', 'password'))) {
        //         return response()->json(['message' => 'Login successful', 'user' => Auth::user()]);
        //     }

        //     return response()->json(['error' => 'Invalid credentials'], 401);
        // }
