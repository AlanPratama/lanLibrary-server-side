<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Str;

class OfficerController extends Controller
{

    public function activingUser($slug)
    {
        if (Auth::user()->role != 'user') {
            $user = User::where('slug', $slug)->first();

            if ($user) {
                $message = null;
                if ($user->verified == 'Not Verified') {
                    $user->verified = 'Verified';
                    $user->save();

                    $message = "SUCCESS VERIFIED USER";
                } else if ($user->verified == 'Verified') {
                    $user->verified = 'Not Verified';
                    $user->save();

                    $message = "SUCCESS UNVERIFIED USER";
                }

                return response()->json([
                    'status' => "success",
                    "message" => $message,
                    "data" => $user,
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'USER NOT FOUND',
                ]);
            }
        }else {
            return response()->json([
                'status' => 'error',
                'message' => 'YOUR CANNOT ACCESS THIS ENDPOINT',
            ]);
        }
    }



    public function getUser()
    {
        if (Auth::user()->role == 'user') {
            return response()->json([
                'status' => 'error',
                'message' => 'YOU ARE NOT LIB MANAGER'
            ]);
        } else {
            if (Auth::user()->role == 'admin') {
                $user = User::where('id', '!=', Auth::user()->id)->paginate(5);
            } else {
                $user = User::where('id', '!=', Auth::user()->id)
                    ->where('role', '=', 'admin')
                    ->paginate(5);
            }

            return response()->json([
                'status' => 'success',
                'data' => $user,
            ]);
        }
    }


    public function addUser(Request $req)
    {
        $validator = FacadesValidator::make($req->all(), [
            'role' => 'required',
            'position' => 'required',
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'phone' => 'required',
            'proPic' => 'max:5000',
            'username' => 'required|unique:users,username',
            'password' => 'required',
            'confirmPassword' => 'same:password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'from' => 'validator',
                'message' => $validator->errors()
            ]);
        }


        try {
            $data = [
                'proPic' => '/assets/404-user-img.png',

                'role' => $req->role,
                'position' => $req->position,
                'name' => $req->name,
                'email' => $req->email,
                'phone' => $req->phone,
                'username' => $req->username,
                'password' => bcrypt($req->password),
            ];

            if ($req->hasFile('proPic')) {
                $fileName = Str::slug($req->username) . '.' . $req->file('proPic')->getClientOriginalName();
                $path = $req->file('proPic')->storeAs('users', $fileName);
                $data['proPic'] = $path;
            }

            $user = User::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'SUCCESS CREATE A NEW USER',
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


    public function editUser(Request $req, $slug)
    {
        $user = User::where('slug', $slug)->first();

        if ($user) {
            if ($user->role == 'admin' && Auth::user()->role != 'admin') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'YOU ARE NOT ADMIN'
                ], 403);
            } else {
                $validator = FacadesValidator::make($req->all(), [
                    'role' => 'required',
                    'position' => 'required',
                    'name' => 'required',
                    'email' => 'required|unique:users,email,'.$user->id,
                    'phone' => 'required',
                    'proPic' => 'max:5000',
                    'username' => 'required|unique:users,username,'.$user->id,
                    // 'password' => '',
                    // 'confirmPassword' => 'same:password'
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 'error',
                        'from' => 'validator',
                        'message' => $validator->errors()
                    ]);
                }


                try {
                    $data = [
                        'proPic' => $user->proPic,

                        'role' => $req->role,
                        'position' => $req->position,
                        'name' => $req->name,
                        'email' => $req->email,
                        'phone' => $req->phone,
                        'username' => $req->username,
                    ];

                    // if ($req->has('password')) {
                    //     $data['password'] = bcrypt($req->password);
                    // }

                    if ($req->hasFile('proPic')) {
                        if ($user->proPic != '/assets/404-user-img.png') {
                            $pathImg = str_replace('/storage', '', $data['proPic']);
                            Storage::delete($pathImg);
                        }

                        $fileName = Str::slug($req->username) . '.' . $req->file('proPic')->getClientOriginalExtension();
                        $path = $req->file('proPic')->storeAs('users', $fileName);
                        $data['proPic'] = '/storage/' . $path;
                    }

                    $user->slug = null;
                    $user->update($data);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'SUCCESS UPDATED USER INFORMATION',
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
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'USER NOT FOUND',
            ]);
        }
    }

    public function delUser($slug)
    {
        $user = User::where('slug', $slug)->first();

        if ($user) {
            if ($user->role == 'admin' && Auth::user()->role != 'admin') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'YOU ARE NOT AMDIN'
                ], 403);
            } else {
                $user->rentlogs()->delete();
                $user->presents()->delete();
                $user->favorites()->delete();
                $user->reviews()->delete();

                if ($user->proPic != '/assets/404-user-img.png') {
                    $pathImg = str_replace('/storage', '', $user->proPic);
                    Storage::delete($pathImg);
                }

                $user->delete();

                return response()->json([
                    'status' => 'success',
                    'message' => 'SUCCESS DELETED USER',
                ]);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'USER NOT FOUND',
            ]);
        }
    }
}
