<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ayat extends Model
{
    protected $table = 'ayat';

    public function surah(){
        return $this->belongsTo(Surat::class, 'surat_id');
    }

    public function juz(){
        return $this->belongsTo(Juz::class);
    }

    public function page(){
        return $this->belongsTo(Page::class);
    }
}
