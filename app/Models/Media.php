<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends \Dot\Media\Models\Media
{


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'hash', 'used_id'
    ];

}
