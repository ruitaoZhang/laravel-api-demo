<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    // 定义 status 中的值
    const USER_STATUS_NORMAL = 1;
    const USER_STATUS_FREEZE = 2;
    public static $userTypeMap = [
        self::USER_STATUS_NORMAL => '正常',
        self::USER_STATUS_FREEZE => '冻结',
    ];
    /**
     * 追加自定义字段
     */
    protected $appends = ['status_str'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'status_str'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getStatusStrAttribute()
    {
        // 数据库中的status为：1、2.通过改方法进行转换，更能清晰的知道 1、2 对应的值
        return User::$userTypeMap[$this->getAttribute('status')];
    }
}
