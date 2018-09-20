<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Contest extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'goal', 'user_id', 'expired_at', 'start_at'
    ];


    /**
     * Creator
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function creator()
    {
        return $this->hasOne(User::class, "id", "user_id");
    }

    /**
     * Winner User
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function winner()
    {
        return $this->hasOne(User::class, "id", "winner_id");
    }
}
