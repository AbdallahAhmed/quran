<?php

namespace App;

use App\Models\Media;


class User extends \Dot\Users\Models\User
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', "api_token", "username", "code", "backend", "root"
    ];


    /**
     * Photo relation
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function photo()
    {
        return $this->hasOne(Media::class, 'id', 'photo_id');
    }

}
