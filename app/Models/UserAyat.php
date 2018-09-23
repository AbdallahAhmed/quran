<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAyat extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_ayat';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'ayah_id',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

}
