<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'surat';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected  $appends=['page_id'];


    public function ayat()
    {
        return $this->hasMany(Ayat::class, 'surat_id', 'id');
    }

    /**
     * @return mixed
     */
    public function getPageIdAttribute()
    {
        return $this->ayat()->where('numberinsurat', 1)->first()->page_id;
    }
}
