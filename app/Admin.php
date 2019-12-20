<?php

namespace App;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;


class Admin extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $table = 'admins';

    protected $fillable = ['name', 'password'];

    public function findForPassport($username)
    {
        return $this->where('name', $username)->first();
    }
}
