<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Ayat extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ayat';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['edition_id'];

    /**
     *  Surah relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function surah(){
        return $this->belongsTo(Surat::class, 'surat_id');
    }
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('edition', function (Builder $builder) {
            $builder->where('edition_id', '=', 77);
        });
    }


}
