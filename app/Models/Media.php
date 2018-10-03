<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends \Dot\Media\Models\Media
{


    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected  $appends=['thumbnail'];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'hash', 'used_id'
    ];


    /**
     *  Add Thumbnail Sizes
     * @return string
     */
    public function getThumbnailAttribute()
    {
         return  thumbnail($this->path);
    }


}
