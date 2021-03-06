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
    protected $hidden = [ "englishtranslation"];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends=['page_id', 'pages_per_surah'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ayat()
    {
        return $this->hasMany(Ayat::class, 'surat_id', 'id');
    }

    public function getPagesPerSurahAttribute(){
        return $this->ayat()->get()->groupBy('page_id')->keys()->toArray();
    }
    /**
     * @return mixed
     */
    public function getPageIdAttribute()
    {
        return $this->ayat()->orderBy('numberinsurat', "ASC")->limit(1)->first()->page_id;
    }
}
