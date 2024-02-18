<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'username' => 'required|unique:users,username|min:6',
            'password' => 'required|min:6',
            'confirmPassword' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'from' => 'validator',
                'req' => $req->all(),
                'message' => $validator->errors(),
            ]);
        }

        try {
            $req['password'] = bcrypt($req['password']);
            $user = User::create($req->all());

            return response()->json([
                'status' => 'success',
                'data' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'from' => 'tC',
                'message' => $e->getMessage(),
            ]);
        }
    }




    public function register2(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'username' => 'required',
            'etc....' => ''
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ]);
        }

        try {
            $req['password'] = bcrypt($req->password);
            $user = User::create($req->all());

            return response()->json([
                'status' => 'success',
                'data' => $user
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'from' => 'tC',
                'message' => $e->getMessage()
            ]);
        }
    }


    public function login2(Request $req)
    {
        $req->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (!Auth::attempt($req->only('username', 'password'))) {
            return response()->json([
                'status' => 'error',
                'from' => 'Auth::attempt',
                'message' => 'BAD CREDENTIASL',
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('jwt')->plainTextToken;
        $cookie = cookie('jwt', $token, 60 * 12);

        return response()->json([
            'status' => 'success',
            'data' => $user
        ])->withCookie($cookie);
    }






















    public function login(Request $req)
    {
        $req->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        try {
            if (!Auth::attempt($req->only('username', 'password'))) {
                return response()->json([
                    'status' => 'error',
                    'from' => 'hashCheck',
                    'message' => 'BAD CREDENTIALS',
                ], Response::HTTP_BAD_REQUEST);
            }

            $user = Auth::user();

            $token = $user->createToken('token')->plainTextToken;
            $cookie = cookie('jwt', $token, 60 * 24);

            return response()->json([
                'status' => 'success',
                'data' => Auth::user()
            ])->withCookie($cookie);


        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }


    public function user()
    {
        return response()->json([
            'status' => 'success',
            'data' => auth()->user()
        ]);
    }


    public function logout()
    {
        $cookie = Cookie::forget('jwt');

        return response()->json([
            'status' => 'success'
        ])->withCookie($cookie);
    }
}
