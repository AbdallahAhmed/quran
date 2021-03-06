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
    protected $appends = ['is_expired', 'is_joined', 'member_counter', 'is_opened', 'remaining_time'];

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
        'name', 'goal', 'user_id', 'expired_at', 'start_at', 'juz_from', 'juz_to', 'closed_to_winner', "type"
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
     *  Add is_expired property
     * @return bool
     */
    public function getIsExpiredAttribute()
    {
        return $this->expired_at < Carbon::now();
    }

    /**
     *  Add is_expired property
     * @return bool
     */
    public function getIsOpenedAttribute()
    {
        return $this->expired_at >= Carbon::now();
    }


    /**
     *  Add member_counter property
     * @return bool
     */
    public function getMemberCounterAttribute()
    {
        return $this->members()->count();
    }

    /**
     *  Add is_joined property
     * @return bool
     */
    public function getIsJoinedAttribute()
    {
        return $this->members()->where('users.id', fauth()->id())->count() ? true : false;
    }

    /**
     * members relations
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'contests_members', 'contest_id', 'member_id')->withPivot(['join_at', 'pages']);
    }

    /**
     *  Add remaining_time property
     * @return bool
     */
    public function getRemainingTimeAttribute()
    {
        return $this->start_at->diffInHours($this->expired_at) . ':' . $this->start_at->diff($this->expired_at)->format('%I:%S');
    }

    /**
     * surat relations
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function surat()
    {
        return $this->belongsToMany(Surat::class, 'contests_surat', 'contest_id', 'surah_id');
    }

    public function getSuratJuzAttribute()
    {
        return get_contest_surat($this->juz_from, $this->juz_to);
    }

    public function getPagesAttribute()
    {
        $pages = array();
        if ($this->type == "surah") {
            $surat = $this->surat()->get();
            if (count($surat) > 0) {
                foreach ($surat as $surah) {
                    $pages = array_merge($pages, $surah->pages_per_surah);
                }
            }
        } else {
            $pages = get_contest_pages($this->juz_from, $this->juz_to);
        }
        return $pages;
    }

}
