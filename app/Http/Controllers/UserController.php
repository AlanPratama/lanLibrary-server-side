<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function update(Request $req, $slug)
    {
        $user = User::where('slug', $slug)->first();

        if ($user) {

            $validator = Validator::make($req->all(), [
                'name' => 'required|min:6',
                'phone' => 'required|min:6',
                'email' => 'required|min:6|unique:users,email,'.$user->id,
                'username' => 'required|min:6|unique:users,username,'.$user->id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'from' => 'validator fails',
                    'message' => $validator->errors(),
                ]);
            }

            $data = [
                'name' => $req->name,
                'phone' => $req->phone,
                'email' => $req->email,
                'username' => $req->username,
            ];

            $user->save($data);

            return response()->json([
                'status' => 'success',
                'message' => 'SUCCESSFULLY UPDATED',
                'data' => $user,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'USER NOT FOUND',
            ]);
        }

    }


    public function changePass(Request $req, $slug)
    {
        // REQUEST => oldPass | newPass | confirmNewPass
        $user = User::where('slug', $slug)->first();
        $oldPass = bcrypt($req->oldPassword);

        if ($user) {
            if ($user->password == $oldPass) {
                $validator = Validator::make($req->all(), [
                    'newPass' => 'required|min:6',
                    'confirmNewPass' => 'required|same:newPass',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 'error',
                        'from' => 'validator fails',
                        'message' => $validator->errors()
                    ]);
                }

                $user->password = bcrypt($req->newPass);
                $user->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'SUCCESSFULLY CHANGED PASSWORD',
                    'data' => $user,
                ]);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => 'INVALID PASSWORD'
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
