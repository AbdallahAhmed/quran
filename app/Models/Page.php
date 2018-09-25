<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'page';

    protected $hidden = ['id'];

    public function ayat(){
        return $this->hasMany(Ayat::class,'page_id','id');
    }

    public function surat(){
        return $this->belongsToMany(Surat::class,'ayat', 'page_id', 'surat_id')->distinct('surat_id');
    }
}
