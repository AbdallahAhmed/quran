<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Juz extends Model
{
    protected  $table = 'juz';

    public function ayat(){
        return $this->hasMany(Ayat::class);
    }

    public function surat(){
        return $this->belongsToMany(Surat::class,'ayat', 'juz_id', 'surat_id')->distinct('surat_id');
    }
}
