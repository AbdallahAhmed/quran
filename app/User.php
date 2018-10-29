<?php

namespace App;

use App\Models\Ayat;
use App\Models\Contest;
use App\Models\Khatema;
use App\Models\Media;
use Illuminate\Support\Carbon;


class User extends \Dot\Users\Models\User
{


    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['photo'];
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

    /**
     * contests relations
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function contests()
    {
        return $this->belongsToMany(Contest::class, 'contests_members', 'member_id', 'contest_id');
    }


    /**
     * Current contest joined and opened or coming
     * @return mixed
     */
    public function contest()
    {
        return $this->belongsToMany(Contest::class, 'contests_members', 'member_id', 'contest_id');//->where('expired_at', '>=', Carbon::now());
    }

    /**
     * CompletedKhatemas relations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function CompletedKhatemas(){
        return $this->hasMany(Khatema::class)->where('completed', 1);
    }

    /**
     * PendingKhatemas relations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function PendingKhatema(){
        return $this->hasOne(Khatema::class)->where('completed', 0);
    }

    public function ayat(){
        return $this->belongsToMany(Ayat::class, 'users_ayat','user_id', 'ayah_id');
    }


}
