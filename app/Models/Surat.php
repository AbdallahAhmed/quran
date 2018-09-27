<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Surat
 * @package App\Models
 */
class Surat extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'surat';


    /**
     * @var array
     */
    protected $hidden = ["englishname", "englishtranslation"];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends=['page_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ayat()
    {
        return $this->hasMany(Ayat::class, 'surat_id', 'id');
    }

    /**
     * @return mixed
     */
    public function getPageIdAttribute()
    {

        $aya = $this->ayat()->orderBy('numberinsurat', "ASC")->limit(1)->first()->page_id;


        if($aya){

        }
        return $aya;
    }
}
