<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ayat extends Model
{
    protected $table = 'ayat';

    public function surat(){
        return $this->belongsTo(Surat::class);
    }

    public function juz(){
        return $this->belongsTo(Juz::class);
    }

    public function page(){
        return $this->belongsTo(Page::class);
    }
}
