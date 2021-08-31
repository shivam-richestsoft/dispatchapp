<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_ADMIN = 0;
    // const ROLE_STAFF = 1;
    // const ROLE_AGENCY = 2;
    // const ROLE_STREAMER = 3;
    const ROLE_USER = 1;
    const ROLE_DRIVER = 2;


    const MALE = 1;
    const FEMALE = 2;
    const UPLOAD_PICTURE_PATH = "/public/images";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'created_by_id',
        'color_code',
        'have_app',
        'phone'

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function jsonData()
    {
        $json = [];
        $json['id'] = $this->id;
        $json['name'] = $this->name;
        //  $json['email'] = $this->email;
        $json['phone'] = $this->phone;
        $json['is_notification'] = $this->is_notification;
        $json['color_code'] = $this->color_code ?? '';
        $json['have_app'] = $this->have_app;

        return $json;
    }
}
