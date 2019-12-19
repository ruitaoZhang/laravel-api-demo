<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\UserResource;
use App\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    public function test(Request $request)
    {
        // 使用一个 API 资源转换层来过滤敏感信息
        // 返回单一用户
//        $user = User::find(1);
//         return $this->success(new UserResource($user));

        // 返回带分页用户列表
        $user = User::paginate(1);
        return UserResource::collection($user); // 返回带分页信息的内容
//        return $this->success(UserResource::collection($user));// 返回不带分页信息的内容

//        return $this->setStatusCode(201)->success($user);
//        return $this->error('失败');
//        return $this->internalError('服务器异常');
    }

    // 使用扩展包 spatie/laravel-query-builder，该包查询参数名称尽可能遵循 JSON API 规范
    public function testQuery(Request $request)
    {
        $user = QueryBuilder::for(User::class)
//            ->allowedAppends('status_str')
            // ->allowedFilters(['name']) // 模糊查找
            ->allowedFilters(AllowedFilter::exact('name')) // 精确查找
            ->allowedSorts('id') // 排序
            ->get();

        return $this->success($user);
    }
}
