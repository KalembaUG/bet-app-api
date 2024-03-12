<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthController extends Controller
{
    public function signup(Request $request) : Response
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:10'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        // Create a token
        $token = $user->createToken('appToken', ['create', 'update', 'delete'])->plainTextToken;

        // return a response with the new user and his token
        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function login(Request $request) : Response
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        try 
        {
            $user = User::where('email', $request->email)->firstOrFail();

            if (!Hash::check($request->password, $user->password))
            {
                return response(['msg' => "Incorrect password"], 400);
            }
            else
            {
                $token = $user->createToken('appToken')->plainTextToken;
                return response(['user' => $user, 'token' => $token], 200);
            }
        } 
        catch (ModelNotFoundException $e) 
        {
            return response(['msg' => "Invalid email"], 404);
        }
    }

    public function logout($id) : Response
    {
        User::find($id)->tokens()->delete();
        return response(['msg' => 'You are now logged out.'], 200);
    }
}