<?php

namespace App\Http\Controllers\Auth;

use stdClass;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create User
     * @param Request $request
     * @return User 
     */
    public function createUser(Request $request)
    {
        //Validated
        $validateUser = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'password_confirmation' => 'required_with:password|same:password|min:8'
            ]
        );

        if ($validateUser->fails()) {
            return response()->json([
                'errors' => $validateUser->errors()
            ], 422);
        }

        $user = User::create($request->all());

        return response()->json([
            'data' => $user
        ], 201);
    }

    /**
     * Login The User
     * @param Request $request
     * @return User
     */
    public function loginUser(Request $request)
    {
        $validateUser = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required'
            ]
        );

        if ($validateUser->fails()) {
            return response()->json([
                'errors' => $validateUser->errors()
            ], 401);
        }

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'errors' => 'Email & Password does not match with our record.',
            ], 401);
        }

        $user = User::whereEmail($request->email)->first();

        //creo l'oggetto per la risposta
        $user_response = new stdClass();
        $user_response->user = new stdClass();
        $user_response->user->name = $user->name;
        $user_response->user->email = $user->email;

        return response()->json([
            'data' => $user_response,
            'token' => $user->createToken("API TOKEN")->plainTextToken
        ], 200);
    }
}
