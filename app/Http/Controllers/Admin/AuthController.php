<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Http\Controllers\Api\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string'
        ]);

        $admin = new Admin([
            'name' => $request->name,
            'password' => Hash::make($request->password),
        ]);

        $admin->save();

        return $this->success('后台用户创建成功', 201);

    }

    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        $credentials = request(['name', 'password']);
        $admin = Admin::where('name', $request->name)->first();
        \Log::error($request->all());
//
//        dd($admin);
        if (!Hash::check($request->password, $admin->password)) {
            return $this->error('用户名或密码错误', 401);

        }
        /*if (!Auth::guard('admin')->attempt($credentials)) {
            return $this->error('用户名或密码错误', 401);
        }


        $admin = $request->user();*/
        \Log::error('---admin info---');
        \Log::error($admin);

        // 创建访问令牌
        $tokenResult = $admin->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ($request->remember_me)
        {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        $token->save();

        return $this->success([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
        ]);
    }

    public function adminInfo(Request $request)
    {
        return $this->success($request->user());
    }

    public function loginOut(Request $request)
    {
        $request->user()->token()->revoke();

        return $this->success('成功退出');
    }
}
