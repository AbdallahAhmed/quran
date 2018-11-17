<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Juz extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'juz';
public $timestamps = false;
    /**
     *  Ayat relation
     * @return \Illuminate\Database\Eloquent\Relations\hasmany
     */
    public function ayat()
    {
        return $this->hasMany(Ayat::class);
    }

    /**
     *  Surat relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function surat()
    {
        return $this->belongsToMany(Surat::class, 'ayat', 'juz_id', 'surat_id')->distinct('surat_id');
    }
    /**
     *  Surat relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function pages(){
        return $this->belongsToMany(Page::class, 'ayat', 'juz_id', 'page_id')->distinct('page_id');
    }
}
