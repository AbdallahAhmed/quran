<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContestMember extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contests_members';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'member_id',
        'contest_id',
    ];

}
