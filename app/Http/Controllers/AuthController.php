<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Http\Requests\userRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(array(
                "status" => false,
                "errors" => $validator->errors()
            ), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'status' => true,
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
                422
            );
        }
        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json([
                'error' => 'Unautherized'
            ], 401);
        }
        return $this->createNewToken($token);
    }
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
    public function profile()
    {
        return response()->json(auth()->user());
    }
    public function logout()
    {
        $user = auth()->user();
        if (isset($user)) {
            auth()->logout();
            return response()->json([
                'message' => 'Logout Successfully'
            ]);
        } else {
            return response()->json([
                'message' => 'user not login'
            ]);
        }
    }
}
