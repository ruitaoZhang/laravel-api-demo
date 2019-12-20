<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ApiResponse;
    /**
     * create user
     * @param [string] name
     * @param [string] email
     * @param [string] password
     * @return [string] message
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string'
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->save();

        return response()->json([
            'message' => '用户创建成功'
        ], 201);

    }

    /**
     * login user and create token
     *
     * @param [string] email
     * @param [string] password
     * @param [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        $credentials = request(['email', 'password']);

        /*if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => '用户名或密码错误'
            ], 401);
        }

        $user = $request->user();*/

        $user = User::where('email', $request->email)->first();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => '用户名或密码错误'
            ], 401);
        }


        // 以下使用个人访问令牌 -> https://learnku.com/docs/laravel/6.x/passport/5152#personal-access-tokens
        $tokenResult = $user->createToken('Personal Access Token', ['user-api']);
        $token = $tokenResult->token;
        Log::error($tokenResult);
        Log::error($tokenResult->token);
        Log::error($tokenResult->accessToken);
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addMinute();

        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
        ]);
    }

    /**
     * get user info
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userInfo(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * login out user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginOut(Request $request)
    {
        $request->user()->token()->revoke();

        return $this->success('成功退出');
    }
}
