<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\SignUpRequest;
use App\Models\User;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    //
    public function Register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:255',
                'email' => 'email|required',
                'password' => 'required',
                'NumberPhone' => 'required'
            ],
            [
                'name.required' => 'Name must not be empty',
                'name.max' =>  'Maximum 255 characters allowed',
                'email.required' => 'Email must not be empty',
                'email.email' => 'Email invalidate',
                'password.required' => 'Password must not be empty',
                'NumberPhone.required' => 'Phone number must not be empty',
            ]
        );
        if ($validator->fails()) {
            return response([
                "status" => "error",
                "message" => $validator->errors(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        $user = User::create($data);
        return response()->json([
            'metadata' => $user,
            'message' => 'Register users Successfully',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function Login(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>'required',
            'password'=>'required'
        ],[
            'email.required' =>'Email must not be empty',
            'password.required' =>'Password must not be empty'
        ]);

        if($validator->fails()){
            return response([
                "status" => "error",
                "message" => $validator->errors(),
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        if (!auth()->attempt($request->all())) {
            return response([
                "status" => "error",
                "message" => 'Incorrect Details.Please try again',
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        auth()->user()->token = auth()->user()->createToken('API Token')->accessToken;
        return response()->json([
            'metadata' => auth()->user(),
            'message' => 'Login users Successfully',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }
}
