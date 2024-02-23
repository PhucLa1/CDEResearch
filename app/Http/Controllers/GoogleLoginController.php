<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Response;
use App\Models\User;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->user();
        // Đăng nhập hoặc đăng ký người dùng vào hệ thống của bạn dựa trên thông tin $user
        // Chưa có tài khoản thì tạo tài khoản 
        $userExist = User::where('email',$user->email)->first();
        if($userExist == null){
            $dataAdd = [
                'email' => $user->email,
                'name' => $user->name,
                'password' => $user->id,
                'avatar' => $user->avatar,
            ];
            $dataAdd['password'] = bcrypt($dataAdd['password']);
            $user = User::create($dataAdd);
            $user->token = $user->createToken('API Token')->accessToken;
            return response()->json([
                'metadata' => $user,
                'message' => 'Đăng kí thành công',
                'status' => 'success',
                'statusCode' => Response::HTTP_OK
            ], Response::HTTP_OK);
        }
        $userExist->token = $userExist->createToken('API Token')->accessToken;
        return response()->json([
            'metadata' => $userExist,
            'message' => 'Đăng nhập thành công',
            'status' => 'success',
            'statusCode' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }
}
