<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Contest extends Model
{


    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends=['is_expired'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'expired_at',
        'start_at'
    ];
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

    /**
     * Scope Expired
     * @param $query
     * @return mixed
     */
    public function scopeExpired($query)
    {
        return $query->where('expired_at', '<', Carbon::now());
    }

    /**
     * Scope opened
     * @param $query
     * @return mixed
     */
    public function scopeOpened($query)
    {
        return $query->where('expired_at', '>', Carbon::now())->where('start_at', '<', Carbon::now());
    }

    /**
     * Scope coming
     * @param $query
     * @return mixed
     */
    public function scopeComing($query)
    {
        return $query->where([
            'start_at', '<', Carbon::now(),
        ]);
    }

    /**
     *  Add is expired property
     * @return bool
     */
    public function getIsExpiredAttribute()
    {
        return $this->expired_at < Carbon::now();
    }
}
