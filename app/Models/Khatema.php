<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Khatema extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'khatemas';

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
        'user_id', 'completed_pages', 'taken_hours', 'remaining_hours', 'created_at', 'completed_at', 'pages'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['user_id'];

}
